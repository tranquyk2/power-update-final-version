<?php

namespace Modules\BarcodeScan\App\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\BarcodeScan\App\Models\Barcode;
use Modules\BarcodeScan\App\Models\Factory;
use Modules\BarcodeScan\App\Models\Line;

class LineTransformer extends TransformerAbstract
{
    public function transform(Line $model): array
    {
        return $model->toArray();
    }
}
