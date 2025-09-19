<?php

namespace Modules\BarcodeScan\App\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\BarcodeScan\App\Models\Barcode;
use Modules\BarcodeScan\App\Models\BarcodeHistory;

class BarcodeTransformer extends TransformerAbstract
{
    public function __construct()
    {
        $this->availableIncludes = [
            'factory',
            'line',
            'model'
        ];
    }

    public function transform(Barcode|BarcodeHistory $barcode): array
    {
        return $barcode->toArray();
    }

    public function includeFactory(Barcode|BarcodeHistory $barcode)
    {
        return $barcode->factory
            ? $this->item($barcode->factory, new FactoryTransformer())
            : $this->primitive(null);
    }

    public function includeLine(Barcode|BarcodeHistory $barcode)
    {
        return $barcode->line
            ? $this->item($barcode->line, new LineTransformer())
            : $this->primitive(null);
    }

    public function includeModel(Barcode|BarcodeHistory $barcode)
    {
        return $barcode->model
            ? $this->item($barcode->model, new ModelProductTransformer())
            : $this->primitive(null);
    }
}
