<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BlockIpService
{
    public static function block($ip, $minutes, $reason)
    {
        Cache::put("blocked_ip:{$ip}", [
            'reason' => $reason,
            'time'   => now()
        ], now()->addMinutes($minutes));
    }

    public static function isBlocked($ip)
    {
        return Cache::has("blocked_ip:{$ip}");
    }
}
