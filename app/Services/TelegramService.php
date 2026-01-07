<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TelegramService
{
        public static function send($message)
        {
            Http::post("https://api.telegram.org/bot".env('TELEGRAM_BOT_TOKEN')."/sendMessage", [
                'chat_id' => env('TELEGRAM_CHAT_ID'),
                'text' => $message,
                'parse_mode' => 'HTML'
            ]);
        }


    // Gửi 1 lần trong X giây (chống spam)
    public static function sendOnce($key, $message, $seconds = 300)
    {
        if (Cache::has($key)) return;

        Cache::put($key, true, $seconds);
        self::send($message);
    }
}
