<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\TelegramService;

class LoginSuccessListener
{
    public function handle(Login $event)
    {
        \Log::info('LOGIN SUCCESS EVENT TRIGGERED');
        $user = $event->user;
        $ip = request()->ip();

        TelegramService::send(
            "✅ <b>ĐĂNG NHẬP THÀNH CÔNG</b>\n".
            "👤 Tài khoản: {$user->email}\n".
            "🌍 IP: {$ip}\n".
            "🕒 Thời gian: ".now()
        );
    }
}


