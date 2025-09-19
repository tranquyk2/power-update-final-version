<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('histories.index');
});

Route::middleware('auth')->group(function () {
    Route::prefix('histories')->group(function () {
        Route::get('by-days', [HistoryController::class, 'byDay'])->name('histories.by-day');
        Route::get('by-hours', [HistoryController::class, 'byHour'])->name('histories.by-hour');
        Route::get('by-departments', [HistoryController::class, 'byDepartments'])->name('histories.by-dept');
        Route::get('', [HistoryController::class, 'index'])->name('histories.index');
        Route::get('export', [HistoryController::class, 'export'])->name('histories.export');
    });
});

require __DIR__ . '/auth.php';
