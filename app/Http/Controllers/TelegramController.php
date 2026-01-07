<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\TelegramService;
use Carbon\Carbon;

class TelegramController extends Controller
{
    public function handle(Request $request)
    {
        $message = $request->message ?? [];
        $text    = trim($message['text'] ?? '');
        $chatId  = $message['chat']['id'] ?? null;

        // 🔐 CHỈ ADMIN
        if (!$chatId || $chatId != env('TELEGRAM_CHAT_ID')) {
            return response('Unauthorized', 403);
        }

        /**
         * =========================
         * 🚨 /panic → BẬT PANIC
         * =========================
         */
        if ($text === '/panic') {

            Cache::put('panic', true);
            Cache::put('panic_time', now());

            TelegramService::send(
                "🚨 <b>PANIC MODE KÍCH HOẠT</b>\n\n".
                "⛔ Server đã OFF toàn bộ request\n".
                "🕒 ".now()->format('H:i:s d/m/Y')
            );
        }

        /**
         * =========================
         * ✅ /panic off → TẮT PANIC
         * =========================
         */
        elseif ($text === '/panic off') {

            Cache::forget('panic');
            Cache::forget('panic_time');

            TelegramService::send(
                "✅ <b>PANIC MODE ĐÃ TẮT</b>\n\n".
                "🟢 Server ONLINE\n".
                "🕒 ".now()->format('H:i:s d/m/Y')
            );
        }

        /**
         * =========================
         * 📊 /panic status
         * =========================
         */
        elseif ($text === '/panic status') {

            if (!Cache::has('panic')) {
                TelegramService::send(
                    "🟢 <b>SERVER ĐANG HOẠT ĐỘNG</b>\n".
                    "🕒 ".now()->format('H:i:s d/m/Y')
                );
            } else {
                $time = Cache::get('panic_time');

                TelegramService::send(
                    "🚨 <b>PANIC ĐANG BẬT</b>\n\n".
                    "⛔ Server đang OFF\n".
                    "🕒 Từ: ".Carbon::parse($time)->format('H:i:s d/m/Y')."\n".
                    "⌛ Đã OFF: ".Carbon::parse($time)->diffForHumans(now(), true)
                );
            }
        }

        /**
         * =========================
         * 📈 /live → TRAFFIC REALTIME
         * =========================
         */
        elseif ($text === '/live') {

            $traffic = Cache::get('traffic_list', []);
            $alerts  = Cache::get('traffic_alerts', []);

            if (empty($traffic)) {
                TelegramService::send("📊 Chưa có traffic nào.");
                return response('OK');
            }

            $msg = "📈 <b>TRAFFIC REALTIME</b>\n\n";

            foreach ($traffic as $ip => $info) {
                $msg .= "🌐 <b>$ip</b>\n";
                $msg .= "📊 Requests: {$info['count']}\n";
                $msg .= "🕒 Update: {$info['updated_at']}\n";

                if (isset($alerts[$ip])) {
                    $msg .= "🚨 <b>CẢNH BÁO</b>\n";
                }

                $msg .= "\n";
            }

            TelegramService::send($msg);
        }

        /**
         * =========================
         * 🚨 /alerts → IP NGUY HIỂM
         * =========================
         */
        elseif ($text === '/alerts') {

            $alerts = Cache::get('traffic_alerts', []);

            if (empty($alerts)) {
                TelegramService::send("✅ Không có IP nào bị cảnh báo.");
                return response('OK');
            }

            $msg = "🚨 <b>DANH SÁCH IP BẤT THƯỜNG</b>\n\n";

            foreach ($alerts as $ip => $time) {
                $msg .= "🌐 <b>$ip</b>\n";
                $msg .= "🕒 Lúc: $time\n\n";
            }

            TelegramService::send($msg);
        }

        /**
         * =========================
         * ❓ /help
         * =========================
         */
        else {
            TelegramService::send(
                "🤖 <b>SECURITY BOT COMMANDS</b>\n\n".
                "/panic – BẬT PANIC MODE\n".
                "/panic off – TẮT PANIC\n".
                "/panic status – TRẠNG THÁI SERVER\n".
                "/live – XEM TRAFFIC REALTIME\n".
                "/alerts – IP BỊ CẢNH BÁO\n".
                "/help – DANH SÁCH LỆNH"
            );
        }

        return response('OK');
    }
}
