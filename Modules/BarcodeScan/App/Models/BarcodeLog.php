<?php

namespace Modules\BarcodeScan\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BarcodeScan\Database\factories\BarcodeLogFactory;

/**
 * Class BarcodeLog
 * @package Modules\BarcodeScan\App\Models
 * 
 * @property int $id
 * @property int $barcode_id
 * @property string $note
 * @property string $device_name
 * @property string $status
 */
class BarcodeLog extends Model
{
    use HasFactory;

    protected $table = 'barcode_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'barcode_id',
        'device_name',
        'note',
        'status',
    ];

    public function barcode()
    {
        return $this->belongsTo(Barcode::class, 'barcode_id');
    }
}
