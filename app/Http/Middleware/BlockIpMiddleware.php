<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\BlockIpService;

class BlockIpMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        if (BlockIpService::isBlocked($ip)) {
            return response()->view('server-off', [], 503);
        }

        return $next($request);
    }
}
