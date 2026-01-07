<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Services\TelegramService;
use App\Services\BlockIpService;
use App\Services\SecurityLogService;
use App\Services\ServerStateService;

class DetectTraffic
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $ua = strtolower($request->userAgent() ?? '');

        $minuteKey = now()->format('YmdHi');

        // ====== 1) Counter per IP (atomic) ======
        $ipKey = "ip:{$ip}:{$minuteKey}";
        Cache::add($ipKey, 0, now()->addSeconds(120));
        $count = Cache::increment($ipKey);

        // ====== 2) Counter global (để dashboard traffic/phút) ======
        $globalKey = "traffic:{$minuteKey}";
        Cache::add($globalKey, 0, now()->addSeconds(120));
        Cache::increment($globalKey);

        // ====== 3) Chart realtime (30 phút) ======
        $chart = Cache::get('traffic_chart', []);
        $label = now()->format('H:i');
        $chart[$label] = (int)($chart[$label] ?? 0) + 1;

        // giữ 30 điểm gần nhất
        if (count($chart) > 30) {
            $chart = array_slice($chart, -30, 30, true);
        }
        Cache::put('traffic_chart', $chart, now()->addMinutes(90));

        // ====== 4) Ngưỡng ======
        $WARN  = (int) env('SEC_WARN', 40);
        $SCAN  = (int) env('SEC_SCAN', 60);
        $BLOCK = (int) env('SEC_BLOCK', 80);
        $BLOCK_MIN = (int) env('SEC_BLOCK_MINUTES', 60);

        // ====== 5) Log UA nguy hiểm (không block ngay, chỉ log) ======
        if (str_contains($ua, 'sqlmap') || str_contains($ua, 'nikto') || str_contains($ua, 'curl') || str_contains($ua, 'python')) {
            SecurityLogService::log('BOT', $ip, $ua);
        }

        // ====== 6) Cảnh báo ======
        if ($count === $WARN) {
            SecurityLogService::log('WARN', $ip, "{$count} req/phút");
            TelegramService::sendOnce(
                "warn:{$ip}:{$minuteKey}",
                "⚠️ <b>CẢNH BÁO TRAFFIC</b>\n🌐 IP: {$ip}\n📈 {$count} req/phút",
                300
            );
        }

        // ====== 7) Phát hiện scan ======
        if ($count === $SCAN) {
            SecurityLogService::log('SCAN', $ip, "{$count} req/phút");
            TelegramService::sendOnce(
                "scan:{$ip}:{$minuteKey}",
                "🧠 <b>PHÁT HIỆN SCAN</b>\n🌐 IP: {$ip}\n📈 {$count} req/phút",
                300
            );
        }

        // ====== 8) Block IP ======
        if ($count >= $BLOCK) {
            BlockIpService::block($ip, $BLOCK_MIN, 'Flood / Scan');
            SecurityLogService::log('BLOCK', $ip, "{$count} req/phút");

            // Bật panic + tắt server (tuỳ bạn)
            ServerStateService::setPanic(true);
            ServerStateService::setServerOff(true);

            TelegramService::sendOnce(
                "block:{$ip}:{$minuteKey}",
                "🧱 <b>BLOCK IP</b>\n🌐 IP: {$ip}\n📈 {$count} req/phút\n⏱ {$BLOCK_MIN} phút\n🚨 <b>ĐÃ BẬT PANIC + TẮT SERVER</b>",
                300
            );

            return response()->view('server-off', [], 503);
        }

        // Nếu server đang off thì chặn luôn (đỡ tốn tài nguyên)
        if (ServerStateService::isServerOff()) {
            return response()->view('server-off', [], 503);
        }

        return $next($request);
    }
}
