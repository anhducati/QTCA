<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use App\Services\TelegramService;

class ServerLockMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Cache::get('server_off')) {

            // 🔑 Link bí mật mở server
            if ($request->query('unlock') === env('SERVER_SECRET_KEY')) {

                Cache::forget('server_off');
                Cache::forget('server_off_time');

                $ip = $request->ip();
                $time = now()->format('H:i:s d/m/Y');

                // 📣 Báo Telegram
                TelegramService::send(
                    "🔓 <b>SERVER ĐƯỢC MỞ BẰNG LINK BÍ MẬT</b>\n".
                    "🕒 {$time}\n".
                    "🌐 IP mở khóa: {$ip}"
                );

                return response('✅ Server đã mở lại', 200);
            }

            // ⛔ Server đang OFF → giả lỗi Cloudflare
            return response()
                ->view('errors.server_off', [], 500);
        }

        return $next($request);
    }
}
