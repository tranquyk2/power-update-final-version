<?php

namespace App\Http\Middleware;

use Closure;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next): mixed
    {
        if (config('api.key') !== $request->header('x-api-key')) {
            return response()->json([
                'success' => false,
                'message' => 'Api key is invalid',
                'error_code' => null,
                'data' => [],
            ], 400);
        }

        return $next($request);
    }
}
