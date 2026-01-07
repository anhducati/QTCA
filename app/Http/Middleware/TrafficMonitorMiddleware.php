<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use App\Services\DetectTrafficService;

class TrafficMonitorMiddleware
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();

        // 🔢 Đếm traffic theo phút
        $minuteKey = 'traffic:' . now()->format('YmdHi');
        Cache::increment($minuteKey);

        // 🔢 Đếm request theo IP
        $ipKey = "ip:{$ip}";
        $count = Cache::increment($ipKey);
        Cache::put($ipKey, $count, now()->addMinute());

        // 🚨 Detect scan / flood / auto panic
        DetectTrafficService::check($ip, $count);

        return $next($request);
    }
}
