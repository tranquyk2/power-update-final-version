<?php

namespace Modules\BarcodeScan\App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BarcodeScan\App\Models\Factory;
use Modules\BarcodeScan\App\Models\Line;
use Modules\BarcodeScan\App\Models\ModelProduct;

class CategoryController extends BaseApiController
{
    public function addFactory(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
    
            $factory = Factory::query()->create($request->all());
            return $this->respond(true, ['factory' => $factory->toArray()]);
        } catch (\Throwable $th) {
            return $this->respondInternalServerError($th->getMessage());
        }
    }

    public function destroyFactory(Factory $factory): JsonResponse
    {
        $factory->delete();
        return $this->respond(true);
    }

    public function addLine(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'factory_id' => 'required|exists:barcode_factories,id',
            ]);

            $line = Line::query()->create($request->all());
            return $this->respond(true, ['line' => $line->toArray()]);
        } catch (\Exception $e) {
            return $this->respondInternalServerError($e->getMessage());
        }
    }

    public function destroyLine(Line $line): JsonResponse
    {
        $line->delete();
        return $this->respond(true);
    }

    public function addModel(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'model' => 'required|string|unique:barcode_models,model|max:255',
            ]);

            $model = ModelProduct::query()->create($request->all());
            return $this->respond(true, ['model' => $model->toArray()]);
        } catch (\Exception $e) {
            return $this->respondInternalServerError($e->getMessage());
        }
    }

    public function destroyModel(ModelProduct $model): JsonResponse
    {
        $model->delete();
        return $this->respond(true);
    }
}
