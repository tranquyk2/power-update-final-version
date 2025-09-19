<?php

namespace Modules\BarcodeScan\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BarcodeScan\App\Models\Traits\BarcodeTrait;

class BarcodeHistory extends Model
{
    use BarcodeTrait;

    protected $table = 'v_barcodes';

    protected $casts = [
        'datetime' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = [
        'type',
        'status_name',
        'datetime_formated',
    ];

    public function barcode()
    {
        return $this->belongsTo(Barcode::class, 'barcode_id');
    }
}
