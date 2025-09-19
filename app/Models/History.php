<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'slave_id',
        'com_port',
        'kw',
        'kw_draft',
        'hour',
        'day',
        'datetime',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'datetime' => 'datetime:Y-m-d H:i:s',
    ];
}
