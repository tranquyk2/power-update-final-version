<?php

namespace Modules\BarcodeScan\App\Repositories;

use App\Repositories\Criteria\BaseRequestCriteria;
use Modules\BarcodeScan\App\Models\Barcode;
use Modules\BarcodeScan\App\Models\ModelProduct;
use Prettus\Repository\Eloquent\BaseRepository;

class ModelProductRepository extends BaseRepository
{
    public function model(): string
    {
        return ModelProduct::class;
    }

    public function boot()
    {
        $this->pushCriteria(new BaseRequestCriteria());
    }
}
