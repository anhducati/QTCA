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
        // ✅ 1) Verify webhook secret (khuyến nghị)
        $secretHeader   = (string) $request->header('X-Telegram-Bot-Api-Secret-Token');
        $expectedSecret = (string) env('TELEGRAM_WEBHOOK_SECRET', '');

        if ($expectedSecret !== '' && $secretHeader !== $expectedSecret) {
            return response('Forbidden', 403);
        }

        $payload = $request->all();

        // ✅ 2) Lấy message an toàn (telegram có thể gửi edited_message)
        $message = $payload['message'] ?? $payload['edited_message'] ?? [];
        $text    = trim((string)($message['text'] ?? ''));
        $chatId  = $message['chat']['id'] ?? null;

        // ✅ 3) Chỉ cho admin chat_id
        if (!$chatId || (string)$chatId !== (string)env('TELEGRAM_CHAT_ID')) {
            return response('Unauthorized', 403);
        }

        // ✅ 4) Normalize command
        // loại bỏ "@botname" nếu có: "/panic@yourbot"
        $cmdRaw = strtolower($text);
        $cmdRaw = preg_replace('/@[\w_]+/i', '', $cmdRaw);
        $cmdRaw = trim($cmdRaw);

        // helper status
        $isLocked = Cache::has('panic') || Cache::get('server_off') === true;

        // ====== /help ======
        if ($cmdRaw === '/help' || $cmdRaw === 'help') {
            $blocked = Cache::get('blocked_ips', []);
            $blockedCount = is_array($blocked) ? count($blocked) : 0;

            TelegramService::send(
                "🤖 <b>SECURITY BOT - DANH SÁCH LỆNH</b>\n\n".

                "🧯 <b>CHẾ ĐỘ KHẨN (PANIC)</b>\n".
                "• /panic — BẬT chế độ khẩn (khóa server)\n".
                "• /panic_off — TẮT chế độ khẩn (mở server)\n".
                "• /status — Xem trạng thái\n\n".

                "📊 <b>GIÁM SÁT TRAFFIC</b>\n".
                "• /live — Xem traffic realtime (traffic_list)\n".
                "• /alerts — Danh sách IP bị cảnh báo (traffic_alerts)\n\n".

                "🧱 <b>DANH SÁCH CHẶN IP</b>\n".
                "• /blocked — Xem danh sách IP bị chặn\n".
                "• /unblock 1.2.3.4 — Gỡ chặn 1 IP\n".
                "• /unblock_all — Gỡ chặn toàn bộ IP\n\n".

                "🔐 <b>MỞ SERVER BẰNG KEY</b>\n".
                "• /unlock MO_KHOA_123456 — Mở server bằng SERVER_SECRET_KEY\n\n".

                "📌 <b>TRẠNG THÁI NHANH</b>\n".
                "• Server: <b>".($isLocked ? "ĐANG BỊ KHÓA" : "ĐANG HOẠT ĐỘNG")."</b>\n".
                "• IP đang chặn: <b>{$blockedCount}</b>\n".
                "🕒 ".now()->format('H:i:s d/m/Y')
            );

            return response('OK');
        }

        // ====== /panic ======
        if ($cmdRaw === '/panic') {
            Cache::put('panic', true);
            Cache::put('panic_time', now()->toDateTimeString());
            Cache::put('server_off', true);
            Cache::put('server_off_time', now()->toDateTimeString());

            TelegramService::send(
                "🚨 <b>ĐÃ BẬT CHẾ ĐỘ KHẨN</b>\n".
                "⛔ Server đang OFF\n".
                "🕒 ".now()->format('H:i:s d/m/Y')
            );
            return response('OK');
        }

        // ====== /panic_off ======
        if ($cmdRaw === '/panic_off' || $cmdRaw === '/panic off') {
            Cache::forget('panic');
            Cache::forget('panic_time');
            Cache::forget('server_off');
            Cache::forget('server_off_time');

            TelegramService::send(
                "✅ <b>ĐÃ TẮT CHẾ ĐỘ KHẨN</b>\n".
                "🟢 Server ONLINE\n".
                "🕒 ".now()->format('H:i:s d/m/Y')
            );
            return response('OK');
        }

        // ====== /status ======
        if ($cmdRaw === '/status' || $cmdRaw === '/panic status') {
            if (!$isLocked) {
                TelegramService::send(
                    "🟢 <b>SERVER ĐANG HOẠT ĐỘNG</b>\n".
                    "🕒 ".now()->format('H:i:s d/m/Y')
                );
                return response('OK');
            }

            $time = Cache::get('panic_time') ?? Cache::get('server_off_time');
            $timeText = $time ? Carbon::parse($time)->format('H:i:s d/m/Y') : 'Không rõ';

            TelegramService::send(
                "🚨 <b>SERVER ĐANG BỊ KHÓA</b>\n".
                "🕒 Từ: {$timeText}\n".
                "⌛ Thời gian: ".($time ? Carbon::parse($time)->diffForHumans(now(), true) : 'Không rõ')
            );
            return response('OK');
        }

        // ====== /live ======
        if ($cmdRaw === '/live') {
            $traffic = Cache::get('traffic_list', []);
            $alerts  = Cache::get('traffic_alerts', []);

            if (empty($traffic) || !is_array($traffic)) {
                TelegramService::send("📊 Chưa có traffic realtime (traffic_list trống).");
                return response('OK');
            }

            $msg = "📈 <b>TRAFFIC REALTIME</b>\n\n";
            $i = 0;

            foreach ($traffic as $ip => $info) {
                $i++;
                if ($i > 25) { $msg .= "… (còn nữa)\n"; break; }

                $count = $info['count'] ?? 0;
                $updated = $info['updated_at'] ?? '—';

                $msg .= "🌐 <b>{$ip}</b>\n";
                $msg .= "• Requests: {$count}\n";
                $msg .= "• Update: {$updated}\n";
                if (isset($alerts[$ip])) $msg .= "🚨 <b>CẢNH BÁO</b>\n";
                $msg .= "\n";
            }

            TelegramService::send($msg);
            return response('OK');
        }

        // ====== /alerts ======
        if ($cmdRaw === '/alerts') {
            $alerts = Cache::get('traffic_alerts', []);

            if (empty($alerts) || !is_array($alerts)) {
                TelegramService::send("✅ Không có IP nào bị cảnh báo.");
                return response('OK');
            }

            $msg = "🚨 <b>DANH SÁCH IP BẤT THƯỜNG</b>\n\n";
            $i = 0;
            foreach ($alerts as $ip => $time) {
                $i++;
                if ($i > 30) { $msg .= "… (còn nữa)\n"; break; }
                $msg .= "🌐 <b>{$ip}</b>\n";
                $msg .= "🕒 Lúc: {$time}\n\n";
            }

            TelegramService::send($msg);
            return response('OK');
        }

        // ====== /blocked ======
        if ($cmdRaw === '/blocked') {
            $blocked = Cache::get('blocked_ips', []);

            if (empty($blocked) || !is_array($blocked)) {
                TelegramService::send("✅ Không có IP nào đang bị chặn.");
                return response('OK');
            }

            $msg = "🧱 <b>DANH SÁCH IP ĐANG BỊ CHẶN</b>\n\n";
            $i = 0;

            foreach ($blocked as $ip => $info) {
                $i++;
                if ($i > 30) { $msg .= "… (còn nữa, xem trên dashboard)\n"; break; }

                $reason = $info['reason'] ?? '—';
                $expire = $info['expire_at'] ?? '—';

                $msg .= "🌐 <b>{$ip}</b>\n";
                $msg .= "• Lý do: {$reason}\n";
                $msg .= "• Hết hạn: {$expire}\n\n";
            }

            TelegramService::send($msg);
            return response('OK');
        }

        // ====== /unblock <ip> ======
        if (str_starts_with($cmdRaw, '/unblock ')) {
            $parts = preg_split('/\s+/', trim($text));
            $ip = $parts[1] ?? '';

            if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
                TelegramService::send("❌ IP không hợp lệ. Ví dụ: /unblock 1.2.3.4");
                return response('OK');
            }

            Cache::forget("block:{$ip}");

            $list = Cache::get('blocked_ips', []);
            if (is_array($list)) {
                unset($list[$ip]);
                Cache::put('blocked_ips', $list, now()->addHours(6));
            }

            TelegramService::send("✅ Đã gỡ chặn IP: <b>{$ip}</b>");
            return response('OK');
        }

        // ====== /unblock_all ======
        if ($cmdRaw === '/unblock_all') {
            $list = Cache::get('blocked_ips', []);
            if (is_array($list)) {
                foreach ($list as $ip => $info) {
                    Cache::forget("block:{$ip}");
                }
            }
            Cache::put('blocked_ips', [], now()->addHours(6));

            TelegramService::send("✅ Đã gỡ chặn toàn bộ IP.");
            return response('OK');
        }

        // ====== /unlock <key> ======
        if (str_starts_with($cmdRaw, '/unlock ')) {
            $parts = preg_split('/\s+/', trim($text));
            $key = $parts[1] ?? '';
            $secret = (string) env('SERVER_SECRET_KEY', '');

            if ($secret === '' || $key !== $secret) {
                TelegramService::send("❌ Sai key mở server.");
                return response('OK');
            }

            Cache::forget('server_off');
            Cache::forget('server_off_time');
            Cache::forget('panic');
            Cache::forget('panic_time');

            TelegramService::send("🔓 <b>SERVER ĐÃ MỞ</b>\n🕒 ".now()->format('H:i:s d/m/Y'));
            return response('OK');
        }

        TelegramService::send("❓ Lệnh không hợp lệ. Gõ /help để xem danh sách lệnh.");
        return response('OK');
    }
}
