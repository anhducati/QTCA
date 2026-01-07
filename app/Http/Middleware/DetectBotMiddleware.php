<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\BlockIpService;
use App\Services\SecurityLogService;
use App\Services\TelegramService;

class DetectBotMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ua = strtolower($request->userAgent() ?? '');
        $ip = $request->ip();

        $badUA = ['curl','python','sqlmap','nikto','wget','httpclient'];

        foreach ($badUA as $bad) {
            if (str_contains($ua, $bad)) {

                BlockIpService::block($ip, (int)env('SEC_BLOCK_MINUTES', 60), "Bot UA: {$bad}");
                SecurityLogService::log('BOT', $ip, "UA={$bad}");

                TelegramService::sendOnce(
                    "botblock:{$ip}",
                    "🧱 <b>AUTO BLOCK (UA)</b>\n🌐 IP: {$ip}\n🧩 UA: {$bad}\n⏱ ".(int)env('SEC_BLOCK_MINUTES', 60)." phút",
                    300
                );

                return response()->view('server-off', [], 503);
            }
        }

        return $next($request);
    }
}
