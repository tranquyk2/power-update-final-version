<?php

namespace Modules\BarcodeScan\App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\BarcodeScan\App\Exports\HistoryExport;
use Modules\BarcodeScan\App\Models\Barcode;
use Modules\BarcodeScan\App\Repositories\BarcodeHistoryRepository;
use Modules\BarcodeScan\App\Repositories\BarcodeLogRepository;
use Modules\BarcodeScan\App\Repositories\BarcodeRepository;
use Modules\BarcodeScan\App\Transformers\BarcodeTransformer;

class BarcodeController extends BaseApiController
{
    public function __construct(
        protected BarcodeRepository $barcodeRepository,
        protected BarcodeHistoryRepository $barcodeHistoryRepository,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $barcodes = $this->barcodeRepository
                ->with(['model', 'factory', 'line'])
                ->orderBy('id', 'desc');

            $barcodes = $request->get('pagination') == 'true'
                ? $barcodes->paginate($request->get('size', 30))
                : $barcodes->get();
            $barcodes = $this->transform($barcodes, BarcodeTransformer::class, 'barcodes');

            return $this->respond(true, $barcodes);
        } catch (\Exception $e) {
            return $this->respondInternalServerError($e->getMessage());
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function histories(Request $request)
    {
        try {
            $barcodes = $this->barcodeHistoryRepository
                ->with(['model', 'factory', 'line'])
                ->orderBy('id', 'desc');

            $barcodes = $request->get('pagination') == 'true'
                ? $barcodes->paginate($request->get('size', 30))
                : $barcodes->get();
            $barcodes = $this->transform($barcodes, BarcodeTransformer::class, 'barcodes');

            return $this->respond(true, $barcodes);
        } catch (\Exception $e) {
            return $this->respondInternalServerError($e->getMessage());
        }
    }

    public function exportHistories(Request $request)
    {
        try {
            ini_set('memory_limit', '256M');
            
            $barcodes = $this->barcodeHistoryRepository
                ->with(['model', 'factory', 'line'])
                ->orderBy('id', 'desc');

            if ((clone $barcodes)->count() > 10000) {
                return $this->respondBadRequest('Maximum 10k records. Please filter your search.');
            }

            $now = now()->format('YmdHis');
            $file = "excel/barcode-histories/Histories_$now.xlsx";
            Excel::store(new HistoryExport($barcodes->get()), $file, 'public');

            return $this->respond(true, [
                'link' => url("/storage/$file"),
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalServerError($e->getMessage());
        }
    }

    public function reportByLines()
    {
        try {
            $data = $this->barcodeRepository
                ->statistics();

            return $this->respond(
                true,
                [
                    'statistics' => $this->statistics($data),
                    'chart_data' => $this->formatChartData($data),
                ]
            );
        } catch (\Exception $e) {
            return $this->respondInternalServerError($e->getMessage());
        }
    }

    private function formatChartData($data)
    {
        $lines = [];

        foreach ($data as $item) {
            $line = $item->line_id;

            if (!isset($lines[$line])) {
                $lines[$line] = [
                    'input' => 0,
                    'output' => 0,
                ];
            }

            if ($item->type_id == 1) {
                $lines[$line]['input'] += $item->count;
            } elseif ($item->type_id == 2) {
                $lines[$line]['output'] += $item->count;
            }
        }

        return [
            'categories' => array_keys($lines),
            'series' => [
                [
                    'name' => 'Input',
                    'data' => array_column($lines, 'input'),
                ],
                [
                    'name' => 'Output',
                    'data' => array_column($lines, 'output'),
                ],
            ],
        ];
    }

    private function statistics($data)
    {
        $result = [];

        foreach ($data as $item) {
            $line = $item->line_id;
            $model = $item->model_id;
            $type = $item->type_id;
            $count = $item->count;

            $result[$line][$model] ??= ['input' => 0, 'output' => 0];

            if ($type == Barcode::IN) {
                $result[$line][$model]['input'] += $count;
            } elseif ($type == Barcode::OUT) {
                $result[$line][$model]['output'] += $count;
            }
        }

        $formattedData = [];
        foreach ($result as $line => $models) {
            foreach ($models as $model => $counts) {
                $formattedData[] = [
                    'line_id' => $line,
                    'model_id' => $model,
                    'input' => $counts['input'],
                    'output' => $counts['output'],
                ];
            }
        }

        return $formattedData;
    }
}
