<?php

namespace App\Transformers;

use App\Transformers\Serializer\CustomArraySerializer;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use \Illuminate\Support\Collection as SupportCollection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

trait CanTransform
{
    protected function transform($data, $transformer, $resourceKey = null, $includes = [], $excludes = []): array
    {
        $request = app(Request::class);

        $fractal = (new Manager())
            ->setSerializer(new CustomArraySerializer())
            ->parseIncludes($request->get('include') . ',' . (is_array($includes) ? implode(',', $includes) : $includes))
            ->parseExcludes($request->get('exclude') . ',' . (is_array($excludes) ? implode(',', $excludes) : $excludes));

        if ($data instanceof LengthAwarePaginator) {
            $result = new Collection($data->items(), app($transformer), $resourceKey);
            $result->setPaginator(new IlluminatePaginatorAdapter($data));
        } elseif (is_array($data) || $data instanceof SupportCollection) {
            $result = new Collection($data, app($transformer), $resourceKey);
        } else {
            $result = new Item($data, app($transformer), $resourceKey);
        }

        return $fractal->createData($result)->toArray();
    }
}
