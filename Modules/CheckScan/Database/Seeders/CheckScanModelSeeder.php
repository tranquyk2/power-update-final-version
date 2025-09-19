<?php

namespace Modules\CheckScan\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CheckScanModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('check_scan_models')->truncate();
        DB::table('check_scan_models')->insert([
            ['code' => 'OMS-EP300W-12V-CV',    'barcode' => '8811900200'],
            ['code' => 'OMS-EP100F-12V-CV',    'barcode' => '8811000200'],
            ['code' => 'OMS-EP200F-12V-CV',    'barcode' => '8811700200'],
            ['code' => 'OMS-EP300F-12V-CV',    'barcode' => '8812100200'],
            ['code' => 'OMS-EP100F-24V-CV',    'barcode' => '8811001200'],
            ['code' => 'OMP-EP200PFC-12V-UL-CV',    'barcode' => '8841600200'],
            ['code' => 'SMPS-OMN-EP150W-200V-CC',    'barcode' => '8821313200'],
            ['code' => 'SMPS-OMN-EP150W-200V-CC-1M',    'barcode' => '8821313202'],
            ['code' => 'SMPS-OMP-EP100PFC-12V-UL-CV',    'barcode' => '8840900200'],
            ['code' => 'OMS-EP100W-12V-CV',    'barcode' => '8810900200'],
            ['code' => 'OMS-EP500S-12V-CV',    'barcode' => '8812300201'],
            ['code' => 'OMS-EP60W-12V-CV',    'barcode' => '8810600200'],
            ['code' => 'OMN-EP150W-200V-CC-Z',    'barcode' => '8821313201'],
            ['code' => 'OMS-EP150-36V-CC',    'barcode' => '8821305200'],
            ['code' => 'OMS-EP100-36V-CC',    'barcode' => '8820905200'],
            ['code' => 'OMS-EP050S-36V-CC-N',    'barcode' => '8820505200'],
            ['code' => 'IPS-EP60W-12V-CV-UL',    'barcode' => '8810600102'],
            ['code' => 'OMN-EP200W-270V-CC',    'barcode' => '8821621200'],
            ['code' => 'OMN-EP200W-270V-CC-1M',    'barcode' => '8821621201'],
            ['code' => 'IPS-EP100W-24V-CV-UL',    'barcode' => '8810901103'],
            ['code' => 'OMS-EP100WN-12V-CV-EX',    'barcode' => '8810900201'],
            ['code' => 'IPS-EP100WN-12V-CV',    'barcode' => '8810900102'],
            ['code' => 'OMS-EP200F-12V-CV-A',    'barcode' => '8811700202'],
            ['code' => 'OMS-EP300F-12V-CV-A',    'barcode' => '8812100201'],
            ['code' => 'OMP-EP300PFC-12V-UL-CV',    'barcode' => '8842000200'],
            ['code' => 'OMS-EP100F-12V-CV-A',    'barcode' => '8811000201'],
            ['code' => 'OMS-EP300W-24V-CV',    'barcode' => '8811901200'],
            ['code' => 'IPS-EP100WN-24V-CV-DO',    'barcode' => '8810901102'],
            ['code' => 'OMS-EP250-36V-CC',    'barcode' => '8821805200'],
            ['code' => 'OMS-EP100WN-24V-CV-EX',    'barcode' => '8810901201'],
            ['code' => 'OMS-EP100WN-12V-CV-T',    'barcode' => '8810900202'],
            ['code' => 'OMS-EP100F-12V-CV-T',    'barcode' => '8811000203'],
            ['code' => 'OMS-EP300F-12V-CV-T',    'barcode' => '8812100203'],
            ['code' => 'OMS-EP60W-12V-CV-T',    'barcode' => '8810600202'],
            ['code' => 'OMS-EP60W-24V-CV',    'barcode' => '8810601200'],
            ['code' => 'IPS-EP100WN-12V-CV-T',    'barcode' => '8810900103'],
            ['code' => 'OMS-EP500F-12V-CV',    'barcode' => '8812300204'],
            ['code' => 'OMS-EP500F-12V-CV-A',    'barcode' => '8812300203'],
            ['code' => 'OMS-EP60W-12V-CV-UL',    'barcode' => '8810600201'],
            ['code' => 'NWP-EP350W-12V-CV',    'barcode' => '8812600300'],
            ['code' => 'NWP-EP350W-24V-CV',    'barcode' => '8812601300'],
            ['code' => 'NWP-EP150W-12V-CV',    'barcode' => '8811300300'],
            ['code' => 'NWP-EP150W-24V-CV',    'barcode' => '8811301300'],
            ['code' => 'NWP-EP100W-24V-CV',    'barcode' => '8810901300'],
            ['code' => 'NWP-EP100W-12V-CV',    'barcode' => '8810900300'],
            ['code' => 'NWP-EP200W-24V-CV',    'barcode' => '8811601300'],
            ['code' => 'NWP-EP200W-12V-CV',    'barcode' => '8811600300'],
            ['code' => 'NWP-EP75W-12V-CV',    'barcode' => '8810700300'],
            ['code' => 'NWP-EP75W-24V-CV',    'barcode' => '8810701300'],
            ['code' => 'NWP-EP50W-12V-CV',    'barcode' => '8810500300'],
            ['code' => 'NWP-EP50W-24V-CV',    'barcode' => '8810501300'],
            ['code' => 'OMS-EP100F-12V-CV-UO',    'barcode' => '8811000204'],
            ['code' => 'OMS-EP200F-12V-CV-UO',    'barcode' => '8811700204'],
            ['code' => 'OMS-EP300F-12V-CV-UO',    'barcode' => '8812100204'],
            ['code' => 'OMS-EP500F-12V-CV-UO',    'barcode' => '8812300205'],
            ['code' => 'SMPS-OMP-EP60PFC-12V-UL-CV',    'barcode' => '8840600200'],
            ['code' => 'OMS-EP300F-24V-CV',    'barcode' => '8812101200'],
            ['code' => 'IPS-EP30W-12V-CV-T',    'barcode' => '8810200102'],
            ['code' => 'IPS-EP150WN-24V-CV',    'barcode' => '8811301201'],
            ['code' => 'OMS-EP60W-24V-CV-EX AC PLUE',    'barcode' => '8810601201'],
            ['code' => 'OMN-EP150W-200V-BP',    'barcode' => '8821313206'],
            ['code' => 'OMS-EP500F-24V-CV-A',    'barcode' => '8812301203'],
            ['code' => 'OMS-EP100F-24V-CV-A',    'barcode' => '8811001202'],
            ['code' => 'OMS-EP300F-24V-CV-A',    'barcode' => '8812101201'],
            ['code' => 'OMS-EP300W-24V-CV-A',    'barcode' => '8811901201'],
            ['code' => 'OMS-EP500F-24V-CV',    'barcode' => '8812301200'],
        ]);
    }
}
