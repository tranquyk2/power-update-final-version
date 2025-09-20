<?php

namespace Modules\CheckScan\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\CheckScan\App\Imports\CheckBarcodeScanImport;
use Modules\CheckScan\App\Models\CheckScan;

//class CheckScanJob implements ShouldQueue
class CheckScanJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $scans = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (empty($this->scans)) {
            return;
        }

        foreach ($this->scans as $scan) {
            $import = new CheckBarcodeScanImport();
            Excel::import($import, storage_path('app/public/' . $scan['file']));
            $data = array_map(function ($item) use ($scan) {
                return [
                    'barcode' => $item['barcode'],
                    'result' => $item['result'],
                    'datetime' => $item['datetime'] ?? null,
                    'model' => $scan['model'] ?? null,
                    'file_name' => $scan['file_name'] ?? null,
                ];
            }, $import->getData());

            //Bắt đầu đưa vào database
            DB::table('check_scans_tmp')->truncate();
            DB::table('check_scans_tmp')->insert($data);
            $rows = DB::table('check_scans_tmp')
                ->select(
                    'barcode',
                    'result',
                    'model',
                    'file_name',
                    'datetime'
                )
                ->get()
                ->map(function ($item) {
                    return (array)$item;
                })
                ->toArray();


            // Insert từng bản ghi vào partition table theo tháng
            foreach ($rows as $row) {
                \App\Helpers\MonthlyPartitionHelper::insertMonthly('check_scans', $row, $row['datetime'] ?? null);
            }

            Log::info(print_r($rows, true));

            // Xóa file tạm
            Storage::disk('public')->delete($scan['file']);
        }
    }
}
