<?php

namespace App\Providers;

use App\Models\History;
use App\Observers\HistoryObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        History::observe(HistoryObserver::class);
    }
}
