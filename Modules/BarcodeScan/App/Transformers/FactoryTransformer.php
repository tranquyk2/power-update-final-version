<?php

namespace Modules\BarcodeScan\App\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\BarcodeScan\App\Models\Barcode;
use Modules\BarcodeScan\App\Models\Factory;

class FactoryTransformer extends TransformerAbstract
{
    public function transform(Factory $model): array
    {
        return $model->toArray();
    }
}
