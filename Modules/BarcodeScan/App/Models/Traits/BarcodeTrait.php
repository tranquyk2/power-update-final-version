<?php

namespace Modules\BarcodeScan\App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\BarcodeScan\App\Models\Barcode;
use Modules\BarcodeScan\App\Models\Factory;
use Modules\BarcodeScan\App\Models\Line;
use Modules\BarcodeScan\App\Models\ModelProduct;

/**
 * Trait BarcodeTrait
 * @package Modules\BarcodeScan\App\Models\Traits
 * 
 * Relationships
 * @property Factory $bcFactory
 * @property Line $line
 * @property ModelProduct $model
 */
trait BarcodeTrait
{
    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_id');
    }

    public function line(): BelongsTo
    {
        return $this->belongsTo(Line::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(ModelProduct::class, 'model_id');
    }

    public function getTypeAttribute(): ?string
    {
        return match ($this->type_id) {
            Barcode::IN => 'Input',
            Barcode::OUT => 'Output',
            default => null,
        };
    }

    public function getStatusNameAttribute()
    {
        return match ($this->status) {
            'ok' => 'OK',
            'ng' => 'NG',
            'duplicate' => 'Duplicate',
            default => null,
        };
    }

    public function getDatetimeFormatedAttribute(): ?string
    {
        return $this->datetime?->format('d/m/Y H:i:s');
    }
}
