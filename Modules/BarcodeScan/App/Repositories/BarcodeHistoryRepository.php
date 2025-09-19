<?php

namespace Modules\BarcodeScan\App\Repositories;

use App\Repositories\Criteria\BaseRequestCriteria;
use Modules\BarcodeScan\App\Models\Barcode;
use Modules\BarcodeScan\App\Models\BarcodeHistory;
use Modules\BarcodeScan\App\Models\BarcodeLog;
use Prettus\Repository\Eloquent\BaseRepository;

class BarcodeHistoryRepository extends BaseRepository
{
    public function model(): string
    {
        return BarcodeHistory::class;
    }

    public function boot()
    {
        $this->pushCriteria(new BaseRequestCriteria());
    }
}
