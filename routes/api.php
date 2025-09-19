<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Middleware\HasTokenMiddleware;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware([JwtMiddleware::class])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});


use App\Http\Controllers\PartitionInsertController;

Route::prefix('histories')
    ->middleware([ApiKeyMiddleware::class])
    ->group(function () {
        Route::get('', [HistoryController::class, 'index']);
        Route::get('fetch', [HistoryController::class, 'fetch']);
        Route::post('', [HistoryController::class, 'store']);
    });

// API insert barcodes/check_scans partition theo tháng
Route::post('partition/barcodes', [PartitionInsertController::class, 'insertBarcode']);
Route::post('partition/check-scans', [PartitionInsertController::class, 'insertCheckScan']);
