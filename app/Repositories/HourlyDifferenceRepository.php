<?php

namespace App\Repositories;

use App\Models\HourlyDifference;
use Prettus\Repository\Eloquent\BaseRepository;

class HourlyDifferenceRepository extends BaseRepository
{
    public function model(): string
    {
        return HourlyDifference::class;
    }
}
