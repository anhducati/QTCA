<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;


class SecurityDashboardController extends Controller
{
public function index()
{
return view('security.dashboard');
}


public function data()
{
return response()->json([
'panic' => Cache::has('panic'),
'traffic' => Cache::get('traffic:' . now()->format('YmdHi'), 0),
'blocked' => Cache::get('blocked_ips', []),
]);
}
}