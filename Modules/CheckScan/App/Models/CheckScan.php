<?php

namespace Modules\CheckScan\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\CheckScan\Database\factories\CheckScanFactory;

class CheckScan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'barcode',
        'result',
        'model',
        'factory_id',
        'line_id',
        'file_name',
        'datetime',
    ];
}
