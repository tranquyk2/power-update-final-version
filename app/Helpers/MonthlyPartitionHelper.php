<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonthlyPartitionHelper
{

    /**
     * Tạo bảng partition theo tháng dựa trên bảng gốc, truyền tháng cụ thể
     * @param string $baseTable
     * @param string|null $month (format: 'Ym')
     * @return bool
     */
    public static function createMonthlyTable($baseTable, $month = null)
    {
        // Escape tên bảng chống SQL injection
        $baseTable = str_replace('`', '', $baseTable);
        $month = $month ?: date('Ym');
        $newTable = $baseTable . '_' . $month;
        $newTable = str_replace('`', '', $newTable);
        $exists = DB::select("SHOW TABLES LIKE ?", [$newTable]);
        if ($exists) return true;
        $create = DB::select("SHOW CREATE TABLE `$baseTable`");
        if (empty($create)) return false;
        $sql = $create[0]->{'Create Table'};
        // Thay tên bảng bằng regex, escape đúng
        $pattern = '/CREATE TABLE `'.preg_quote($baseTable, '/').'`/i';
        $replacement = 'CREATE TABLE IF NOT EXISTS `'.$newTable.'`';
        $sql = preg_replace($pattern, $replacement, $sql, 1);
        // Xóa AUTO_INCREMENT (dùng regex linh hoạt)
        $sql = preg_replace('/AUTO_INCREMENT=\d+\s*/i', '', $sql);
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
     * @param string $baseTable
     * @param array $data
     * @param string|null $datetime
     * @return bool
     */
    public static function insertMonthly($baseTable, $data, $datetime = null)
    {
        $month = $datetime ? date('Ym', strtotime($datetime)) : date('Ym');
        $table = $baseTable . '_' . $month;
        $ok = self::createMonthlyTable($baseTable, $month); // Đảm bảo bảng đúng tháng tồn tại
        if (!$ok) {
            throw new \Exception('Partition table create failed for '.$table);
        }
        return DB::table($table)->insert($data);
    }
}
