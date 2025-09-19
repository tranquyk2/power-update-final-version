<?php

namespace Modules\BarcodeScan\App\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\BarcodeScan\App\Models\Factory;
use Modules\BarcodeScan\App\Models\Line;
use Modules\BarcodeScan\App\Models\ModelProduct;

class InfoController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $factories = Factory::all();
        $lines = Line::all();
        $models = ModelProduct::all();

        return $this->respond(
            true,
            [
                'factories' => $factories,
                'lines' => $lines,
                'models' => $models,
            ]
        );
    }
}
