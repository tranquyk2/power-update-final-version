<?php

namespace Modules\BarcodeScan\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BarcodeScan\Database\factories\LineFactory;

class Line extends Model
{
    use HasFactory;

    protected $table = 'barcode_lines';
    
    protected $fillable = [
        'name',
        'factory_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
