<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\BlockIpService;
use App\Services\TelegramService;

class DetectBotMiddleware
{
    public function handle($request, Closure $next)
    {
        $ua = strtolower($request->userAgent() ?? '');
        $ip = $request->ip();

        $badUA = ['curl','python','sqlmap','nikto','wget','httpclient'];

        foreach ($badUA as $bad) {
            if (str_contains($ua, $bad)) {

                BlockIpService::block($ip, 60, "Bot UA: {$bad}");

                TelegramService::sendOnce(
                    "block:{$ip}",
                    "🧱 <b>AUTO BLOCK IP</b>\nIP: {$ip}\nUA: {$bad}\n⏱ 60 phút",
                    300
                );

                return response()->view('server-off', [], 503);
            }
        }

        return $next($request);
    }
}
