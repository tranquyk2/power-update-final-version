<?php

namespace App\Repositories\Criteria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class BaseRequestCriteria implements CriteriaInterface
{
    public function apply($model, RepositoryInterface $repository)
    {
        /** @var Builder $model */

        $_model = $model->getModel();
        $table = $_model->getTable();
        $connectionName = $_model->getConnection()->getName();

        if (request('filter') && is_array(request('filter'))) {
            foreach (request('filter') as $filter) {
                if (Schema::connection($connectionName)->hasColumn($table, $filter['field'])) {
                    self::filterByColumn($filter, $model);
                }
            }
        }

        if (request('ids')) {
            $ids = explode(',', request('ids'));
            $model = $model->whereIn($model->getModel()->getKeyName(), $ids);
        }

        return $model;
    }

    /**
     * @param $filter
     * @param Builder $model
     * @return void
     */
    private static function filterByColumn($filter, &$model): void
    {
        if ($filter['type'] == 'string') {
            $values = explode(' ', $filter['value']);
            $values = array_filter($values);

            $model = $model->where(function ($q) use ($values, $filter) {
                foreach ($values as $value) {
                    $q->orWhere($filter['field'], 'like', "%$value%");
                }
            });
        }

        if (in_array($filter['type'], ['datetime', 'date'])) {
            if (in_array($filter['operator'], ['lte', 'gte'])) {
                $datetime = $filter['operator'] == 'lte' ? "{$filter['from']}:59" : "{$filter['from']}:00";
                $model = $model->where(
                    $filter['field'],
                    $filter['operator'] == 'lte' ? '<=' : '>=', $filter['type'] === 'date' ? $filter['from'] : $datetime
                );
            }

            if ($filter['operator'] == 'between') {
                $datetime = ["{$filter['from']}:00", "{$filter['to']}:59"];
                $date = [$filter['from'], $filter['to']];
                $model = $model->whereBetween($filter['field'], $filter['type'] === 'date' ? $date : $datetime);
            }
        }

        if ($filter['type'] == 'number') {
            if (in_array($filter['operator'], ['lte', 'gte'])) {
                $model = $model->where($filter['field'], $filter['operator'] == 'lte' ? '<=' : '>=', $filter['from']);
            }

            if ($filter['operator'] == 'between') {
                $model = $model->whereBetween($filter['field'], [$filter['from'], $filter['to']]);
            }
        }

        if ($filter['type'] == 'select') {
            $model = $model->where($filter['field'], $filter['value']);
        }

        if ($filter['type'] == 'eq') {
            $model = $model->where($filter['field'], '=', $filter['value']);
        }

        if (isset($filter['value']) && $filter['type'] == 'in' && is_array($filter['value'])) {
            $model = $model->whereIn($filter['field'], $filter['value']);
        }
    }
}
