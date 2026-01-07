<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\TelegramService;
use App\Services\BlockIpService;

class DetectTraffic
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        // 🔑 Key theo phút
        $key = "ip:{$ip}:" . now()->format('YmdHi');

        // ⏱ TTL 120s
        $ttl = now()->addSeconds(120);

        // 🔢 Đếm request (FILE CACHE SAFE)
        $count = Cache::get($key, 0) + 1;
        Cache::put($key, $count, $ttl);

        /**
         * 🚦 NGƯỠNG
         */
        $WARN  = 20;
        $SCAN  = 40;
        $BLOCK = 60;

        /**
         * ⚠️ CẢNH BÁO
         */
        if ($count === $WARN) {
            TelegramService::sendOnce(
                "warn:{$ip}",
                "⚠️ <b>CẢNH BÁO TRAFFIC</b>\n".
                "🌐 IP: {$ip}\n".
                "📈 {$count} req/phút",
                300
            );
        }

        /**
         * 🧠 PHÁT HIỆN SCAN
         */
        if ($count === $SCAN) {
            TelegramService::sendOnce(
                "scan:{$ip}",
                "🧠 <b>PHÁT HIỆN SCAN</b>\n".
                "🌐 IP: {$ip}\n".
                "📈 {$count} req/phút",
                300
            );
        }

        /**
         * 🧱 BLOCK IP 60 PHÚT
         */
        if ($count >= $BLOCK) {

            BlockIpService::block($ip, 60, 'Flood / Scan');

            TelegramService::sendOnce(
                "block:{$ip}",
                "🧱 <b>BLOCK IP</b>\n".
                "🌐 IP: {$ip}\n".
                "📈 {$count} req/phút\n".
                "⏱ Thời gian: 60 phút",
                300
            );

            return response()->view('server-off', [], 503);
        }

        return $next($request);
    }
}
