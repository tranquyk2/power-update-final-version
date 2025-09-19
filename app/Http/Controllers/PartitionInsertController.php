<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\MonthlyPartitionHelper;

class PartitionInsertController extends Controller
{
    // Insert cho barcodes/check_scans theo tháng
    public function insertBarcode(Request $request)
    {
        $data = $request->all();
        MonthlyPartitionHelper::insertMonthly('barcodes', $data, $data['created_at'] ?? null);
        return response()->json(['success' => true]);
    }

    public function insertCheckScan(Request $request)
    {
        $data = $request->all();
        MonthlyPartitionHelper::insertMonthly('check_scans', $data, $data['created_at'] ?? null);
        return response()->json(['success' => true]);
    }
}
