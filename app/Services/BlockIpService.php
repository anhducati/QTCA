<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class BlockIpService
{
    public static function block(string $ip, int $minutes, string $reason = ''): void
    {
        $minutes = max(1, (int)$minutes);

        // Key từng IP để middleware check (TTL theo minutes)
        Cache::put("block:{$ip}", true, now()->addMinutes($minutes));

        // List hiển thị dashboard: TTL dài hơn để không mất dữ liệu
        $list = Cache::get('blocked_ips', []);
        $list[$ip] = [
            'expire_at' => now()->addMinutes($minutes)->format('H:i d/m/Y'),
            'reason'    => $reason,
        ];
        Cache::put('blocked_ips', $list, now()->addDays(2));
    }

    public static function unblock(string $ip): void
    {
        Cache::forget("block:{$ip}");

        $list = Cache::get('blocked_ips', []);
        unset($list[$ip]);

        Cache::put('blocked_ips', $list, now()->addDays(2));
    }

    public static function isBlocked(string $ip): bool
    {
        return Cache::has("block:{$ip}");
    }
}
