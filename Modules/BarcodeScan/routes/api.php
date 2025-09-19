<?php

use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\BarcodeScan\App\Http\Controllers\Api\BarcodeController;
use Modules\BarcodeScan\App\Http\Controllers\Api\BarcodeScanController;
use Modules\BarcodeScan\App\Http\Controllers\Api\CategoryController;
use Modules\BarcodeScan\App\Http\Controllers\Api\ModelProductController;
use Modules\BarcodeScan\App\Http\Controllers\InfoController;

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

Route::prefix('barcode-scan')
    ->middleware([ApiKeyMiddleware::class])
    ->group(function () {
        Route::get('', [BarcodeScanController::class, 'index']);

        Route::prefix('models')->group(function () {
            Route::get('', [ModelProductController::class, 'index']);
        });

        Route::get('infos', [InfoController::class, 'index']);
        Route::post('store', [BarcodeScanController::class, 'store']);
    });

Route::prefix('barcode')
    ->middleware([JwtMiddleware::class])
    ->group(function () {
        Route::get('infos', [InfoController::class, 'index']);

        Route::get('', [BarcodeController::class, 'index']);
        Route::get('histories', [BarcodeController::class, 'histories']);
        Route::get('histories/export', [BarcodeController::class, 'exportHistories']);
        Route::get('reports/by-lines', [BarcodeController::class, 'reportByLines']);

        Route::prefix('categories')->group(function () {
            Route::post('factory', [CategoryController::class, 'addFactory']);
            Route::delete('factory/{factory}', [CategoryController::class, 'destroyFactory']);

            Route::post('line', [CategoryController::class, 'addLine']);
            Route::delete('line/{line}', [CategoryController::class, 'destroyLine']);

            Route::post('model', [CategoryController::class, 'addModel']);
            Route::delete('model/{model}', [CategoryController::class, 'destroyModel']);
        });
    });
