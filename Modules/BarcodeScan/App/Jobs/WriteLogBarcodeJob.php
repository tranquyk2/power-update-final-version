<?php

namespace Modules\BarcodeScan\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\BarcodeScan\App\Events\BarcodeScanEvent;
use Modules\BarcodeScan\App\Models\BarcodeHistory;
use Modules\BarcodeScan\App\Models\BarcodeLog;

class WriteLogBarcodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected $data
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $bc = BarcodeLog::query()->create([
            'barcode_id' => $this->data['barcode_id'],
            'note' => $this->data['note'],
            'device_name' => $this->data['device_name'],
            'status' => $this->data['status'],
        ]);

        $bc = BarcodeHistory::query()->with(['model', 'line', 'factory'])->find($bc->id);
        event(new BarcodeScanEvent($bc));
    }
}
