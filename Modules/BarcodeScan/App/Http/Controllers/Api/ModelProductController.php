<?php

namespace Modules\BarcodeScan\App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Js;
use Modules\BarcodeScan\App\Repositories\ModelProductRepository;
use Modules\BarcodeScan\App\Transformers\ModelProductTransformer;

class ModelProductController extends BaseApiController
{
    public function __construct(
        protected ModelProductRepository $modelProductRepository
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $models = $this->modelProductRepository;
        $models = request('pagination') == 'true' ? $models->paginate(request('size', 30)) : $models->get();
        $models = $this->transform($models, ModelProductTransformer::class, 'models');

        return $this->respond(true, $models);
    }
}
