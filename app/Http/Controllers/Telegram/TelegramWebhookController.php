<?php

namespace App\Http\Controllers\Telegram;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TelegramService;
use App\Services\ServerStateService;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1) Xác thực secret header (Telegram hỗ trợ secret token)
        $secret = $request->header('X-Telegram-Bot-Api-Secret-Token');
        if (!$secret || $secret !== env('TELEGRAM_WEBHOOK_SECRET')) {
            return response('forbidden', 403);
        }

        $update = $request->all();

        // 2) Lấy chat id / text
        $message = $update['message'] ?? $update['edited_message'] ?? null;
        if (!$message) return response()->json(['ok' => true]);

        $chatId = $message['chat']['id'] ?? null;
        $text = trim($message['text'] ?? '');

        // 3) Chỉ cho phép đúng chat id admin
        if ((string)$chatId !== (string)env('TELEGRAM_CHAT_ID')) {
            return response()->json(['ok' => true]);
        }

        // 4) Parse lệnh
        $cmd = strtolower($text);

        if ($cmd === '/start' || $cmd === '/help') {
            TelegramService::send(
                "📌 <b>Lệnh điều khiển</b>\n".
                "/server_on - Bật server\n".
                "/server_off - Tắt server\n".
                "/panic_on - Bật chế độ khẩn\n".
                "/panic_off - Tắt chế độ khẩn\n".
                "/status - Xem trạng thái"
            );
            return response()->json(['ok' => true]);
        }

        if ($cmd === '/status') {
            $info = ServerStateService::getInfo();
            TelegramService::send(
                "🧾 <b>TRẠNG THÁI</b>\n".
                "⛔ Server: ".($info['server_off'] ? "OFF" : "ON")."\n".
                ($info['server_off_time'] ? "🕒 Off lúc: {$info['server_off_time']}\n" : "").
                "🚨 Panic: ".($info['panic'] ? "ON" : "OFF")."\n".
                ($info['panic_time'] ? "🕒 Panic lúc: {$info['panic_time']}\n" : "")
            );
            return response()->json(['ok' => true]);
        }

        if ($cmd === '/server_off') {
            ServerStateService::setServerOff(true, 'telegram');
            TelegramService::send("⛔ <b>ĐÃ TẮT SERVER</b>\n🕒 ".now()->format('H:i:s d/m/Y'));
            return response()->json(['ok' => true]);
        }

        if ($cmd === '/server_on') {
            ServerStateService::setServerOff(false, 'telegram');
            TelegramService::send("✅ <b>ĐÃ BẬT SERVER</b>\n🕒 ".now()->format('H:i:s d/m/Y'));
            return response()->json(['ok' => true]);
        }

        if ($cmd === '/panic_on') {
            ServerStateService::setPanic(true);
            TelegramService::send("🚨 <b>ĐÃ BẬT CHẾ ĐỘ KHẨN</b>\n🕒 ".now()->format('H:i:s d/m/Y'));
            return response()->json(['ok' => true]);
        }

        if ($cmd === '/panic_off') {
            ServerStateService::setPanic(false);
            TelegramService::send("🟢 <b>ĐÃ TẮT CHẾ ĐỘ KHẨN</b>\n🕒 ".now()->format('H:i:s d/m/Y'));
            return response()->json(['ok' => true]);
        }

        TelegramService::send("❓ Lệnh không hợp lệ. Gõ /help để xem danh sách lệnh.");
        return response()->json(['ok' => true]);
    }
}
