<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class PanicMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Cache::has('panic')) {
            return response()->view('errors.server_off', [], 500);
        }

        return $next($request);
    }
}
