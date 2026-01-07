<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ServerStateService
{
    const KEY_SERVER_OFF = 'server_off';
    const KEY_SERVER_OFF_TIME = 'server_off_time';

    const KEY_PANIC = 'panic';
    const KEY_PANIC_TIME = 'panic_time';

    public static function isServerOff(): bool
    {
        return Cache::get(self::KEY_SERVER_OFF) === true;
    }

    public static function setServerOff(bool $off, ?string $by = null): void
    {
        if ($off) {
            Cache::put(self::KEY_SERVER_OFF, true, now()->addDays(7));
            Cache::put(self::KEY_SERVER_OFF_TIME, now()->format('H:i:s d/m/Y'), now()->addDays(7));
        } else {
            Cache::forget(self::KEY_SERVER_OFF);
            Cache::forget(self::KEY_SERVER_OFF_TIME);
        }

        // panic thường đi kèm server_off (tuỳ bạn). Mình tách riêng:
        if (!$off) {
            // không tự tắt panic khi bật server, tuỳ ý bạn
        }
    }

    public static function isPanic(): bool
    {
        return Cache::get(self::KEY_PANIC) === true;
    }

    public static function setPanic(bool $on): void
    {
        if ($on) {
            Cache::put(self::KEY_PANIC, true, now()->addDays(7));
            Cache::put(self::KEY_PANIC_TIME, now()->format('H:i:s d/m/Y'), now()->addDays(7));
        } else {
            Cache::forget(self::KEY_PANIC);
            Cache::forget(self::KEY_PANIC_TIME);
        }
    }

    public static function getInfo(): array
    {
        return [
            'server_off' => self::isServerOff(),
            'server_off_time' => Cache::get(self::KEY_SERVER_OFF_TIME),
            'panic' => self::isPanic(),
            'panic_time' => Cache::get(self::KEY_PANIC_TIME),
        ];
    }
}
