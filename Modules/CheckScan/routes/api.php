<?php

use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Support\Facades\Route;
use Modules\CheckScan\App\Http\Controllers\CheckScanController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

Route::prefix('check-scan')
    ->middleware([ApiKeyMiddleware::class])
    ->group(function () {
        Route::get('models', [CheckScanController::class, 'models']);
        Route::post('upload-file', [CheckScanController::class, 'uploadFile']);
        Route::get('packing', [CheckScanController::class, 'packing']);

        Route::post('{model}/increment/box', [CheckScanController::class, 'incrementBox']);
    });
