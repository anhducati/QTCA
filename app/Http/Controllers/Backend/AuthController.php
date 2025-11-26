<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{


    public function login() {
        if(Auth::id() > 0) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Bạn đã đăng nhập. Nếu muốn đăng nhập tài khoản khác xin vui lòng đăng xuất.');
        }

        return view('backend.auth.login');
    }

    public function handleLogin(AuthRequest $request) {
        $credentials = [
            'email' =>  $request->input('email'),
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công');
        }

        return redirect()->back()->with('error', 'Email hoặc mật khẩu không chinh xác');

    }

    public function register() {


        return view('backend.auth.register');
    }

    public function logout(Request $request)
    {
    //     if(Auth::user()->id_admin) {

    //         Auth::logout();
    //         $request->session()->invalidate();
    //         $request->session()->regenerateToken();

    //         return redirect()->route('client.login')->with('success', 'Đăng xuất thành công');
    //     }else {
    //         Auth::logout();
    //         $request->session()->invalidate();
    //         $request->session()->regenerateToken();

    //         return redirect()->route('client.login')->with('success', 'Đăng xuất thành công');
    //     }
            Auth::logout();
             $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('client.login')->with('success', 'Đăng xuất thành công');

     }
}
