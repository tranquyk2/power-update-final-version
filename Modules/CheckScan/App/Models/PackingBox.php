<?php

namespace Modules\CheckScan\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CheckScan\Database\factories\PackingBoxFactory;

/**
 * PackingBox Model
 * @property int $id
 * @property string $model
 * @property int $box_count
 * @property \Illuminate\Support\Carbon|null $date
 */
class PackingBox extends Model
{
    use HasFactory;

    protected $table = 'check_scan_packing_box';

    protected $fillable = [
        'model',
        'box_count',
        'date',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function incrementBox(): int
    {
        $this->box_count++;
        $this->save();

        return $this->box_count;
    }
}
