<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @property int $id
 * @property int $year
 * @property int $month
 * @property int $day
 * @property int $slave_id
 * @property int $cost
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $last_executed_at
 */
class Cost extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'cost',
        'last_executed_at',
        'slave_id',
    ];

    protected $casts = [
        'last_executed_at' => 'datetime:Y-m-d H:i:s',
    ];
}
