<?php

namespace Modules\BarcodeScan\App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\BarcodeScan\App\Jobs\WriteLogBarcodeJob;
use Modules\BarcodeScan\App\Models\Barcode;
use Modules\BarcodeScan\App\Models\ModelProduct;

class BarcodeService
{
    public static function store(array $data): ?Barcode
    {
        try {
            if (strlen($data['code']) >= 40) {
                throw new \Exception("Mã quét không hợp lệ");
            }

            /** @var ModelProduct $model */
            $model = Cache::rememberForever("model_product_{$data['model_id']}", function () use ($data) {
                return ModelProduct::query()->find($data['model_id']);
            });
            $shortProdCode = substr($model->product_code, 3, strlen($model->product_code) - 1);
            $barcode = preg_replace('/[^a-zA-Z0-9]/', '', $data['code']);
            if (str_contains($barcode, $model->product_code) || str_contains($barcode, $shortProdCode)) {
                $data['status'] = 'ok';
            } else {
                $data['note'] = "Mã sản phẩm không khớp với model";
                $data['status'] = 'ng';
            }

            DB::beginTransaction();
            /** @var Barcode|null $barcode */
            $barcode = Barcode::query()
                ->where('factory_id', $data['factory_id'])
                ->where('type_id', $data['type_id'])
                ->firstWhere('code', $data['code']);
            $log = [];

            if ($barcode) {
                $note = "Mã quét tồn tại với trạng thái " . ($barcode->status == 'ok' ? 'OK' : 'NG');
                $status = 'duplicate';
                $log = [
                    ...$data,
                    'status' => $status,
                    'note' => $note,
                    'barcode_id' => $barcode->id,
                ];
                $barcode->status_name = "NG";
                $barcode->note = $note;
                $barcode->status = 'ng';
            } else {
                // $check = self::checkSuitable($data);
                // if ($check !== true) {
                //     throw new \Exception($check);
                // }

                // Insert vào partition table theo tháng
                \App\Helpers\MonthlyPartitionHelper::insertMonthly('barcodes', $data, $data['datetime'] ?? null);
                // Lấy lại bản ghi vừa insert từ bảng partition đúng tháng
                $month = isset($data['datetime']) ? date('Ym', strtotime($data['datetime'])) : date('Ym');
                $partitionTable = 'barcodes_' . $month;
                $barcode = \DB::table($partitionTable)
                    ->where('factory_id', $data['factory_id'])
                    ->where('type_id', $data['type_id'])
                    ->where('code', $data['code'])
                    ->orderByDesc('id')
                    ->first();
                $log = [
                    'barcode_id' => $barcode->id,
                    'note' => "Đã thêm mã quét với trạng thái " . ($barcode->status == 'ok' ? 'OK' : 'NG'),
                    'device_name' => $data['device_name'],
                    'status' => $barcode->status,
                ];
            }

            if (!empty($log)) {
                WriteLogBarcodeJob::dispatch($log);
            }
            DB::commit();

            return $barcode;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private static function checkSuitable(array $data)
    {
        if ($data['factory_id'] == Barcode::LINE_AI && $data['type_id'] == Barcode::IN) {
            return true;
        }

        //Nếu out ở line AI thì check xem có tồn tại mã IN không
        if (
            $data['factory_id'] == Barcode::LINE_AI &&
            $data['type_id'] == Barcode::OUT
        ) {
            if (Barcode::query()->where('factory_id', $data['factory_id'])
                ->where('type_id', Barcode::IN)
                ->where('code', $data['code'])->exists()
            ) {
                return true;
            } else {
                return "Sản phẩm chưa được input ở AI";
            }
        }

        //Nếu in ở line SMT thì check xem có tồn tại mã OUT ở AI không
        if (
            $data['factory_id'] == Barcode::LINE_SMT &&
            $data['type_id'] == Barcode::IN
        ) {
            if (Barcode::query()->where('factory_id', Barcode::LINE_AI)
                ->where('type_id', Barcode::OUT)
                ->where('code', $data['code'])->exists()
            ) {
                return true;
            } else {
                return "Sản phẩm chưa được output ở AI";
            }
        }

        //Nếu out ở line SMT thì check xem có tồn tại mã IN ở SMT không
        if (
            $data['factory_id'] == Barcode::LINE_SMT &&
            $data['type_id'] == Barcode::OUT
        ) {
            if (Barcode::query()->where('factory_id', $data['factory_id'])
                ->where('type_id', Barcode::IN)
                ->where('code', $data['code'])->exists()
            ) {
                return true;
            } else {
                return "Sản phẩm chưa được input ở SMT";
            }
        }

        return "Không tìm thấy hành động phù hợp";
    }
}
