<?php

namespace Modules\BarcodeScan\App\Repositories\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class LimitCriteria implements CriteriaInterface
{
    public function apply($model, RepositoryInterface $repository)
    {
        $hasFilterDate = false;
        if (request('filter') && is_array(request('filter'))) {
            foreach (request('filter') as $filter) {
                if (self::hasFilterDate($filter)) {
                    $hasFilterDate = true;
                    return;
                }
            }
        }

        if (!$hasFilterDate) {
            $model = $model->where(function ($query) {
                $query->where('datetime', '>=', now()->subDays(15));
            });
        }
    }

    private static function hasFilterDate($filter): bool
    {
        if (in_array($filter['type'], ['datetime', 'date'])) {
            return true;
        }

        return false;
    }
}
