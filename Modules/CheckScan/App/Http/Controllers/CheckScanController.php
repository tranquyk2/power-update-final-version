<?php

namespace Modules\CheckScan\App\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\CheckScan\App\Http\Requests\Api\CheckScanRequest;
use Modules\CheckScan\App\Http\Requests\Api\PackingRequest;
use Modules\CheckScan\App\Jobs\CheckScanJob;
use Modules\CheckScan\App\Models\CheckScan;
use Modules\CheckScan\App\Models\CheckScanModel;
use Modules\CheckScan\App\Models\PackingBox;
use Modules\CheckScan\App\Models\PackingCheck;

class CheckScanController extends BaseApiController
{
    public function models(): JsonResponse
    {
        $models = Cache::remember('check_scan_models', now()->addDay(), function () {
            return CheckScanModel::all();
        });

        return $this->respond(true, ['models' => $models]);
    }

    public function uploadFile(CheckScanRequest $request): JsonResponse
    {
        $scans = $request->all()['scans'] ?? [];
        foreach ($scans as $key => $scan) {
            if (isset($scan['file']) && $scan['file'] instanceof \Illuminate\Http\UploadedFile) {
                $path = "check-scan/" . uniqid() . "_" . $scan['file']->getClientOriginalName();
                Storage::disk('public')->put($path, $scan['file']->get());
                $scans[$key]['file'] = $path;
                $scans[$key]['file_name'] = $scan['file']->getClientOriginalName();
            } else {
                unset($scans[$key]);
            }
        }

        dispatch(new CheckScanJob($scans));

        return $this->respond(true);
    }

    public function packing(PackingRequest $request): JsonResponse
    {
        $checkScan = CheckScan::query()
            ->firstWhere('barcode', $request->input('barcode'));
        $isPacked = false;

        if (!$checkScan) {
            PackingCheck::query()->updateOrCreate(
                ['barcode' => $request->input('barcode')],
                [
                    'model' => $request->input('model'),
                    'result' => 'FT NG'
                ]
            );
            return $this->respond(true, ['check_scan' => null, 'is_packed' => $isPacked]);
        }

        if (!($request->get('unsave') == 'true')) {
            /** @var PackingCheck $checked */
            $checked = PackingCheck::query()
                ->firstWhere('barcode', $request->input('barcode'));

            $isPacked = $checked && $checked->result === 'PASS';

            if (!$checked) {
                PackingCheck::query()->create($checkScan->toArray());
            }

            if ($checked && !($checked->result === 'PASS')) {
                $checked->update([
                    'result' => $checkScan->result
                ]);
            }
        }

        return $this->respond(true, ['check_scan' => $checkScan, 'is_packed' => $isPacked]);
    }

    public function incrementBox(Request $request, string $model): JsonResponse
    {
        /** @var PackingBox $model */
        $model = PackingBox::query()->firstOrCreate(
            ['model' => $model, 'date' => now()->format('Y-m-d')],
            ['box_count' => 0]
        );
        $model->incrementBox();

        return $this->respond(true, ['box' => $model->toArray()]);
    }
}
