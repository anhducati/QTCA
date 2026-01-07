<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use App\Services\TelegramService;

class LoginFailedListener
{
    public function handle(Failed $event)
    {
        TelegramService::send(
            "❌ <b>ĐĂNG NHẬP THẤT BẠI</b>\n".
            "👤 Email: ".($event->credentials['email'] ?? 'Không rõ')."\n".
            "🌍 IP: ".request()->ip()."\n".
            "🕒 Thời gian: ".now()
        );
    }
}
