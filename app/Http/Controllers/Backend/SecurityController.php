<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use App\Services\BlockIpService;
use App\Services\ServerStateService;

class SecurityController extends Controller
{
    public function index()
    {
        return view('backend.security.index');
    }

    public function unblock(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|string|max:64',
        ]);

        BlockIpService::unblock($request->ip_address);

        return response()->json([
            'ok' => true,
            'message' => "Đã gỡ chặn IP {$request->ip_address}",
        ]);
    }

    // API bật/tắt panic từ dashboard
    public function panic(Request $request)
    {
        $request->validate([
            'enabled' => 'required|boolean',
        ]);

        ServerStateService::setPanic((bool)$request->enabled);

        return response()->json([
            'ok' => true,
            'panic' => ServerStateService::isPanic(),
        ]);
    }

    public function data()
    {
        $info = ServerStateService::getInfo();

        $panic = $info['panic'] || $info['server_off'];

        $trafficKey = 'traffic:' . now()->format('YmdHi');
        $traffic = (int) Cache::get($trafficKey, 0);

        $blocked = Cache::get('blocked_ips', []);
        $logs = Cache::get('security_logs', []);

        $chart = Cache::get('traffic_chart', []);
        if (empty($chart)) {
            $chart = [];
            for ($i = 29; $i >= 0; $i--) {
                $time = now()->subMinutes($i)->format('H:i');
                $chart[$time] = 0;
            }
        }

        return response()->json([
            'panic'   => $panic,
            'server_off' => (bool) $info['server_off'],
            'server_off_time' => $info['server_off_time'],

            'traffic' => $traffic,
            'blocked' => $blocked,
            'logs'    => array_slice($logs, 0, 50),
            'chart'   => $chart,
            'time'    => now()->format('H:i:s'),
        ]);
    }
}
