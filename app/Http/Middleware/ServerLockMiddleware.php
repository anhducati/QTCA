<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ServerStateService;
use App\Services\TelegramService;

class ServerLockMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!ServerStateService::isServerOff()) {
            return $next($request);
        }

        // Link bí mật mở server: ?unlock=SERVER_SECRET_KEY
        if ($request->query('unlock') === env('SERVER_SECRET_KEY')) {

            ServerStateService::setServerOff(false);

            TelegramService::send(
                "🔓 <b>SERVER ĐÃ ĐƯỢC MỞ BẰNG LINK BÍ MẬT</b>\n".
                "🕒 ".now()->format('H:i:s d/m/Y')."\n".
                "🌐 IP mở khóa: ".$request->ip()
            );

            return response('✅ Server đã mở lại', 200);
        }

        return response()->view('server-off', [], 503);
    }
}
