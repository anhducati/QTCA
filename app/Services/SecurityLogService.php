<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SecurityLogService
{
    public static function log(string $type, string $ip, string $info = ''): void
    {
        $logs = Cache::get('security_logs', []);

        array_unshift($logs, [
            'type' => strtoupper($type),
            'ip'   => $ip,
            'info' => $info,
            'time' => now()->format('H:i:s d/m/Y'),
        ]);

        $logs = array_slice($logs, 0, 200);
        Cache::put('security_logs', $logs, now()->addDays(2));
    }
}
