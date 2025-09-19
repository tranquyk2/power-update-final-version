<?php

namespace Modules\CheckScan\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CheckScan\Database\factories\PackingCheckFactory;

/**
 * PackingCheck Model
 * @property int $id
 * @property string $barcode
 * @property string|null $result
 * @property string|null $model
 */
class PackingCheck extends Model
{
    use HasFactory;

    protected $table = 'check_scan_packings';

    protected $fillable = [
        'barcode',
        'result',
        'model',
    ];
}
