<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public static function send(string $message): void
    {
        try {
            $token = env('TELEGRAM_BOT_TOKEN');
            $chat  = env('TELEGRAM_CHAT_ID');

            if (!$token || !$chat) return;

            Http::connectTimeout(1)
                ->timeout(2)
                ->retry(2, 150)
                ->asForm()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chat,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]);
        } catch (\Throwable $e) {
            Log::warning('[Telegram] send failed: '.$e->getMessage());
        }
    }

    public static function sendOnce(string $key, string $message, int $seconds = 300): void
    {
        if (Cache::has($key)) return;
        Cache::put($key, true, $seconds);
        self::send($message);
    }
}
