<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\MonthlyPartitionHelper;

class CreateMonthlyTables extends Command
{
    protected $signature = 'partition:create-monthly-tables';
    protected $description = 'Tạo bảng partition barcodes/check_scans theo tháng';

    public function handle()
    {
        $ok1 = MonthlyPartitionHelper::createMonthlyTable('barcodes');
        $ok2 = MonthlyPartitionHelper::createMonthlyTable('check_scans');
        if ($ok1 && $ok2) {
            $this->info('Tạo bảng partition thành công!');
        } else {
            $this->error('Tạo bảng partition thất bại!');
        }
    }
}
