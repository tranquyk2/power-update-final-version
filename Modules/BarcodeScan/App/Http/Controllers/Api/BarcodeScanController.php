<?php

namespace Modules\BarcodeScan\App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\BarcodeScan\App\Http\Requests\BarcodeRequest;
use Modules\BarcodeScan\App\Jobs\WriteLogBarcodeJob;
use Modules\BarcodeScan\App\Models\Barcode;
use Modules\BarcodeScan\App\Models\BarcodeLog;
use Modules\BarcodeScan\App\Repositories\BarcodeRepository;
use Modules\BarcodeScan\App\Services\BarcodeService;
use Modules\BarcodeScan\App\Transformers\BarcodeTransformer;

class BarcodeScanController extends BaseApiController
{
    public function __construct(
        protected BarcodeRepository $barcodeRepository,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $barcodes = $this->barcodeRepository->orderBy('id', 'desc');
        $barcodes = $barcodes->paginate($request->get('size', 30));
        $barcodes = $this->transform($barcodes, BarcodeTransformer::class, 'barcodes');

        return $this->respond(true, $barcodes);
    }

    public function store(BarcodeRequest $request): JsonResponse
    {
        try {
            $request->merge([
                'note' => $request->get('status') == 'ok' ? '-' : $request->get('note'),
            ]);

            $barcode = BarcodeService::store($request->all());
            // dd($barcode);

            return $this->respond(
                true,
                data: $this->transform($barcode, BarcodeTransformer::class),
            );
        } catch (\Exception $e) {
            return $this->respondInternalServerError($e->getMessage());
        }
    }
}
