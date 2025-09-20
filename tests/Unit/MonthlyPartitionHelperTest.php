<?php

namespace Tests\Unit;

use App\Helpers\MonthlyPartitionHelper;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MonthlyPartitionHelperTest extends TestCase
    
{
    public function test_create_and_insert_partition_table()
    {
        $baseTable = 'barcodes';
        $month = date('Ym');
        $partitionTable = $baseTable . '_' . $month;
        // Xóa bảng partition nếu đã tồn tại
        DB::statement("DROP TABLE IF EXISTS `$partitionTable`");
        // Tạo bảng partition mới
        $created = MonthlyPartitionHelper::createMonthlyTable($baseTable, $month);
        $this->assertTrue($created, 'Không tạo được bảng partition');
        // Kiểm tra bảng đã tồn tại
        $exists = DB::select("SHOW TABLES LIKE '$partitionTable'");
        $this->assertNotEmpty($exists, 'Bảng partition không tồn tại sau khi tạo');
        // Insert dữ liệu mẫu
        $data = [
            'factory_id' => 1,
            'line_id' => 1,
            'model_id' => 1,
            'code' => 'TESTBARCODE',
            'device_name' => 'TEST_DEVICE',
            'datetime' => now(),
            'type_id' => 1,
            'char_count' => 10,
            'note' => 'unit test',
            'status' => 'ok',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $inserted = MonthlyPartitionHelper::insertMonthly($baseTable, $data, $data['datetime']);
        $this->assertTrue($inserted, 'Insert vào bảng partition thất bại');
        // Kiểm tra dữ liệu đã insert
        $row = DB::table($partitionTable)->where('code', 'TESTBARCODE')->first();
        $this->assertNotNull($row, 'Không tìm thấy dữ liệu vừa insert');
        $this->assertEquals('TEST_DEVICE', $row->device_name);
    }

    public function test_create_and_insert_checkscan_partition_table()
    {
        $baseTable = 'check_scans';
        $month = date('Ym');
        $partitionTable = $baseTable . '_' . $month;
        // Xóa bảng partition nếu đã tồn tại
        DB::statement("DROP TABLE IF EXISTS `$partitionTable`");
        // Tạo bảng partition mới
        $created = MonthlyPartitionHelper::createMonthlyTable($baseTable, $month);
        $this->assertTrue($created, 'Không tạo được bảng partition check_scans');
        // Kiểm tra bảng đã tồn tại
        $exists = DB::select("SHOW TABLES LIKE '$partitionTable'");
        $this->assertNotEmpty($exists, 'Bảng partition check_scans không tồn tại sau khi tạo');
        // Insert dữ liệu mẫu
        $data = [
            'barcode' => 'TESTCHECKSCAN',
            'result' => 'OK',
            'model' => 'MODEL_X',
            'file_name' => 'file.txt',
            'datetime' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $inserted = MonthlyPartitionHelper::insertMonthly($baseTable, $data, $data['datetime']);
        $this->assertTrue($inserted, 'Insert vào bảng partition check_scans thất bại');
        // Kiểm tra dữ liệu đã insert
        $row = DB::table($partitionTable)->where('barcode', 'TESTCHECKSCAN')->first();
        $this->assertNotNull($row, 'Không tìm thấy dữ liệu checkscan vừa insert');
        $this->assertEquals('OK', $row->result);
    }
}