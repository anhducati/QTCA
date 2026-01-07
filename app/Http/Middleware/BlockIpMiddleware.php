<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\BlockIpService;

class BlockIpMiddleware
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();

        if (BlockIpService::isBlocked($ip)) {
            return response()
                ->view('server-off', [
                    'reason' => BlockIpService::info($ip)['reason'] ?? 'Blocked'
                ], 503);
        }

        return $next($request);
    }
}
