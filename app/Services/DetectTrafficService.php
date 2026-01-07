<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DetectTrafficService
{
    public static function check()
    {
        $minuteKey = 'traffic:' . now()->format('YmdHi');
        $count = Cache::get($minuteKey, 0);
        //  ⚠️ Ngưỡng panic (bạn có thể chỉnh)
        if ($count > 50) {
            Cache::put('server_off', true);

            TelegramService::send(
                "🚨 <b>PANIC MODE</b>\n".
                "⚠️ Traffic bất thường: {$count}/phút\n".
                "⛔ Server đã OFF"
            );
        }
    }
}
