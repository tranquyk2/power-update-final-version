<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class PartitionQueryHelper
{
    /**
     * Truy vấn barcode trong tất cả các bảng partition check_scans_YYYYMM (từ tháng hiện tại lùi về trước N tháng)
     * @param string $barcode
     * @param int $monthsBack
     * @return object|null
     */
    public static function findBarcodeInPartitions($barcode)
    {
        $month = now()->format('Ym');
        $table = 'check_scans_' . $month;
        // Kiểm tra bảng tồn tại
        $exists = DB::select("SHOW TABLES LIKE '$table'");
        if ($exists) {
            return DB::table($table)->where('barcode', $barcode)->first();
        }
        return null;
    }
}
