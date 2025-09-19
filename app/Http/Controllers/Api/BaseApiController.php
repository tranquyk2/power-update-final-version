<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Transformers\CanTransform;

abstract class BaseApiController extends Controller
{
    use ResponseTrait, CanTransform;
}
