<?php

namespace Modules\CheckScan\App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class CheckBarcodeScanImport implements ToCollection, WithHeadingRow
{
    protected $data = [];

    public function collection($collection)
    {
        if ($collection->isEmpty()) {
            return;
        }

        $firstRow = collect($collection->first());

        $hasBarcodeColumns = $firstRow->keys()->contains(function ($key) {
            return str_starts_with(strtolower($key), 'barcode') || str_starts_with(strtolower($key), '二维码');
        });

        if (!$hasBarcodeColumns) {
            return;
        }

        $barcodeCount = collect($collection->first())
            ->filter(function ($value, $key) {
                return str_starts_with(strtolower($key), 'barcode') || str_starts_with(strtolower($key), '二维码');
            })
            ->count();

        $keyMap = $this->mapKey($collection->first()->keys());

        try {
            foreach ($collection as $rowUnMapped) {
                $row = $rowUnMapped->mapWithKeys(function ($value, $key) use ($keyMap) {
                    return [$keyMap[$key] ?? strtolower($key) => $value];
                });

                $rowData = [];
                $datetime = $row['date_time'] ?? null;
                $datetimeCount = -1;
                $datetime = $datetime ? preg_replace('/(\d{2})-(\d{2})-(\d{2})$/', '$1:$2:$3', $datetime, count: $datetimeCount) : null;
                if ($datetimeCount <= 0) {
                    try {
                        Carbon::parse($datetime);
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                for ($i = 1; $i <= $barcodeCount; $i++) {
                    $barcodeKey = "barcode{$i}";
                    $resultKey = "result{$i}";

                    if ((($row[$barcodeKey] ?? null) || ($row['barcode'] ?? null)) && (($row[$resultKey] ?? null) || ($row["result"] ?? null))) {
                        $result = ($row[$resultKey] ?? $row["result"] ?? 'FAIL') === 'PASS' ? 'PASS' : 'FAIL';
                        $barcode = $row[$barcodeKey] ?? $row['barcode'] ?? null;
                        $rowData[] = [
                            'barcode' => $barcode,
                            'result' => $result,
                            'datetime' => $datetime,
                        ];
                    }
                }

                if (!empty($rowData)) {
                    $this->data = [...$this->data, ...$rowData];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing CheckBarcodeScanImport: ' . $e->getMessage());
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    private function mapKey($rawKeys)
    {
        return $rawKeys->mapWithKeys(function ($key) {
            if (str_starts_with($key, '二维码')) {
                $number = preg_replace('/\D+/', '', $key); // Lấy số
                return [$key => "barcode$number"];
            }

            if (str_starts_with($key, '测试结果')) {
                $number = preg_replace('/\D+/', '', $key);
                return [$key => "result$number"];
            }

            if (str_starts_with($key, '测试时间')) {
                $number = preg_replace('/\D+/', '', $key);
                return [$key => "date_time$number"];
            }

            return [$key => strtolower(Str::slug($key, '_'))];
        });
    }
}
