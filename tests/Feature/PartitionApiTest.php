<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PartitionApiTest extends TestCase
{
    public function test_partition_api_insert_barcode()
    {
        $data = [
            'factory_id' => 1,
            'line_id' => 1,
            'model_id' => 1,
            'code' => 'API_BARCODE',
            'device_name' => 'API_DEVICE',
            'datetime' => now()->format('Y-m-d H:i:s'),
            'type_id' => 1,
            'char_count' => 10,
            'note' => 'api test',
            'status' => 'ok',
        ];
        $response = $this->postJson('/api/partition/barcodes', $data);
        $response->assertStatus(200);
        $month = date('Ym', strtotime($data['datetime']));
        $partitionTable = 'barcodes_' . $month;
        $row = DB::table($partitionTable)->where('code', 'API_BARCODE')->first();
        $this->assertNotNull($row, 'Không tìm thấy dữ liệu vừa insert qua API');
    }

    public function test_partition_api_insert_checkscan()
    {
        $data = [
            'barcode' => 'API_CHECKSCAN',
            'result' => 'OK',
            'model' => 'MODEL_X',
            'file_name' => 'file.txt',
            'datetime' => now()->format('Y-m-d H:i:s'),
        ];
        $response = $this->postJson('/api/partition/check-scans', $data);
        $response->assertStatus(200);
        $month = date('Ym', strtotime($data['datetime']));
        $partitionTable = 'check_scans_' . $month;
        $row = DB::table($partitionTable)->where('barcode', 'API_CHECKSCAN')->first();
        $this->assertNotNull($row, 'Không tìm thấy dữ liệu checkscan vừa insert qua API');
    }
}
