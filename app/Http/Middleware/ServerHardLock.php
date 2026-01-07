<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServerHardLock
{
    public function handle(Request $request, Closure $next)
    {
        if (Cache::get('server_off') === true) {

            // Cho phép duy nhất route unlock
            if ($request->is('unlock')) {
                return $next($request);
            }

            return response()->view('server-off', [], 503);
        }

        return $next($request);
    }
}
