<?php

namespace Modules\BarcodeScan\App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\BarcodeScan\App\Models\Traits\BarcodeTrait;
use Modules\BarcodeScan\Database\factories\BarcodeFactory;

/**
 * @property int $id
 * @property int $factory_id
 * @property int $line_id
 * @property int $model_id
 * @property string $code
 * @property string $device_name
 * @property Carbon $datetime
 * @property int $type_id
 * @property int $char_count
 * @property string $note
 * @property string $status
 * 
 * 
 */
class Barcode extends Model
{
    use BarcodeTrait;

    const LINE_AI = 1;
    const LINE_SMT = 2;

    const IN = 1;
    const OUT = 2;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'factory_id',
        'line_id',
        'code',
        'device_name',
        'datetime',
        'type_id',
        'char_count',
        'model_id',
        'note',
        'status',
    ];

    protected $casts = [
        'datetime' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = [
        'type',
        'status_name',
        'datetime_formated',
    ];
}
