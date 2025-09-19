<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyPartitionHelper
{
    /**
     * Tạo bảng partition theo tháng dựa trên bảng gốc
     */
    public static function createMonthlyTable($baseTable)
    {
        $month = date('Ym');
        $newTable = $baseTable . '_' . $month;
        $exists = DB::select("SHOW TABLES LIKE '$newTable'");
        if ($exists) return true;
        $create = DB::select("SHOW CREATE TABLE $baseTable");
        if (empty($create)) return false;
        $sql = $create[0]->{'Create Table'};
        $sql = str_replace("CREATE TABLE `$baseTable`", "CREATE TABLE `$newTable`", $sql);
        // Xóa AUTO_INCREMENT nếu có
        $sql = preg_replace('/AUTO_INCREMENT=\\d+ /', '', $sql);
        try {
            DB::statement($sql);
            return true;
        } catch (\Exception $e) {
            Log::error('Partition table create error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Insert vào bảng partition theo tháng
     */
    public static function insertMonthly($baseTable, $data, $datetime = null)
    {
        $month = $datetime ? date('Ym', strtotime($datetime)) : date('Ym');
        $table = $baseTable . '_' . $month;
        self::createMonthlyTable($baseTable); // Đảm bảo bảng tồn tại
        return DB::table($table)->insert($data);
    }
}
