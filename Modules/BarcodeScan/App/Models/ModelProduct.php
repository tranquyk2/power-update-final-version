<?php

namespace Modules\BarcodeScan\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\BarcodeScan\Database\factories\ModelProductFactory;

/**
 * Class ModelProduct
 * @package Modules\BarcodeScan\App\Models
 * 
 * @property int $id
 * @property int $factory_id
 * @property string $model
 * @property string $std_record
 * @property string $product_code
 */
class ModelProduct extends Model
{
    use HasFactory;

    protected $table = 'barcode_models';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'model',
        'std_record',
    ];

    protected $appends = [
        'product_code',
    ];

    public function getProductCodeAttribute(): string
    {
        return str_replace('-','', $this->std_record);
    }
}
