<?php

namespace Modules\BarcodeScan\App\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\BarcodeScan\App\Models\ModelProduct;

class ModelProductTransformer extends TransformerAbstract
{
    public function transform(ModelProduct $model): array
    {
        return $model->toArray();
    }
}
