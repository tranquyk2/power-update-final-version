<?php

namespace App\Http\Controllers\Api;

use App\Enums\DepartmentEnum;
use App\Http\Controllers\Controller;
use App\Repositories\HistoryRepository;
use App\Repositories\HourlyDifferenceRepository;
use App\Transformers\HistoryTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoryController extends BaseApiController
{
    public function __construct(
        protected HistoryRepository $historyRepository,
        protected HourlyDifferenceRepository $hourlyDifferenceRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $histories = $this
            ->historyRepository
            ->paginate($request->get('size', 30));
        $histories = $this->transform($histories, HistoryTransformer::class, 'histories');

        return $this->respond(true, $histories);
    }

    public function store(Request $request): JsonResponse
    {
        $kw = $request->get('kw');
        $request->merge([
            'hour' => now()->hour,
            'day' => now()->day,
            'kw_draft' => $kw,
        ]);
        $history = $this->historyRepository->create($request->all());
        $history = $this->transform($history, HistoryTransformer::class, 'history');

        return $this->respond(true, $history);
    }
}
