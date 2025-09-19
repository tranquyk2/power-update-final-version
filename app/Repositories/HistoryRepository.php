<?php

namespace App\Repositories;

use App\Models\History;
use App\Repositories\Criteria\BaseRequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class HistoryRepository extends BaseRepository
{
    public function model(): string
    {
        return History::class;
    }

    public function boot()
    {
        $this->pushCriteria(new BaseRequestCriteria());
    }
}
