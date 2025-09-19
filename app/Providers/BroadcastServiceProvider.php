<?php

namespace App\Providers;

use App\Http\Middleware\JwtMiddleware;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes(['middleware' => [JwtMiddleware::class]]);

        require base_path('routes/channels.php');
    }
}
