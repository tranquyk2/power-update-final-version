<?php

namespace Modules\BarcodeScan\App\Repositories;

use App\Repositories\Criteria\BaseRequestCriteria;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Modules\BarcodeScan\App\Models\Barcode;
use Prettus\Repository\Eloquent\BaseRepository;

class BarcodeRepository extends BaseRepository
{
    public function model(): string
    {
        return Barcode::class;
    }

    public function boot()
    {
        $this->pushCriteria(new BaseRequestCriteria());
    }

    public function statistics(): Collection
    {
        return $this->applyCriteria()
            ->applyScope()
            ->model
            ->select('model_id', 'line_id', 'type_id', DB::raw('count(id) as count'))
            ->groupBy('line_id', 'type_id', 'model_id')
            ->get();
    }
}
