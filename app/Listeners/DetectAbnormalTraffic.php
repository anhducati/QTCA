<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DetectTrafficService
{
    public static function check($ip, $count)
    {
        // Nếu đã panic rồi thì bỏ qua
        if (Cache::has('panic')) return;

        // ⚠️ Ngưỡng scan (bạn có thể chỉnh)
        if ($count > 60) {

            Cache::put('panic', true);
            Cache::put('panic_time', now());

            TelegramService::send(
                "🚨 <b>AUTO PANIC</b>\n".
                "IP nghi scan: {$ip}\n".
                "📥 Request/phút: {$count}\n".
                "⛔ Server đã OFF"
            );
        }
    }
}
