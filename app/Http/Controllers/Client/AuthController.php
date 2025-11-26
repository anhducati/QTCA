<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\ForgorPasswordMail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Nette\Utils\Random;
use App\Mail\RegisterMail;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Interfaces\UserInterface;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }



    public function login() {

        return view('client.auth.login');

        // return view('client.auth.login-daihoc');
    }

    // public function handle_login(LoginRequest $request) {

    //     $credentials = [
    //         'email' =>  $request->input('email'),
    //         'password' => $request->input('password'),
    //     ];

    //     if (Auth::attempt($credentials)) {
    //         if(!empty(Auth::user()->email_verified_at)) {
    //             $request->session()->regenerate();

    //             // return redirect()->route('client.home')->with('success', 'Đăng nhập thành công');
    //             //Chuyển hương thẳng vào admin
    //             return redirect()->route('admin.blog')->with('success', 'Đăng nhập thành công');

    //         }else {
    //             $userId = Auth::user()->id;
    //             Auth::logout();

    //             $user = $this->userRepository->getUserById($userId);
    //             $user->remember_token = Str::random(40);
    //             $user->save();

    //             Mail::to($user->email)->send(new RegisterMail($user));

    //             return redirect()->back()
    //                 ->with('error', 'Tài khoản chưa kích hoạt vui lòng kiểm tra email của bạn')
    //                 ->with('msg-error', 'Tài khoản chưa kích hoạt vui lòng kiểm tra email của bạn');
    //         }

    //     }


    //     return redirect()->back()
    //         ->with('error', 'Email hoặc mật khẩu không chinh xác')
    //         ->with('msg-error', 'Email hoặc mật khẩu không chinh xác');
    // }
    public function handle_login(LoginRequest $request) {
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
    
            if ($user->status == 1) { 
                $request->session()->regenerate();
    
                
                // return redirect()->route($user->is_admin ? 'admin.blog' : 'client.home')->with('success', 'Đăng nhập thành công');
                return redirect()->route('admin.blog')->with('success', 'Đăng nhập thành công');

            } else {
                // Nếu status là 0 (không được phép đăng nhập)
                Auth::logout();
                return redirect()->back()
                    // ->with('error', 'Tài khoản của bạn không có quyền truy cập')
                    ->with('msg-error', 'Tài khoản của bạn không có quyền truy cập');
            }
        }
    
        // Trường hợp không thành công khi đăng nhập
        return redirect()->back()
            ->with('error', 'Email hoặc mật khẩu không chính xác');
            // ->with('msg-error', 'Email hoặc mật khẩu không chính xác');
    }
    

    public function register() {

        return view('client.auth.register');
    }

    public function handle_register(UserRequest $request) {

        $save = new User();
        $save->name = trim($request->name);
        $save->email = trim($request->email);
        $save->password = Hash::make(trim($request->password));
        $save->remember_token = Str::random(40);
        $save->save();

        Mail::to($save->email)->send(new RegisterMail($save));

        return redirect()->route('client.login')
            ->with('success', 'Chúng tôi đã gửi cho bạn thông báo xác nhận qua Gmail. Vui lòng kiểm tra')
            ->with('msg', 'Chúng tôi đã gửi cho bạn thông báo xác nhận qua Gmail. Vui lòng kiểm tra')
            ->with('type', 'success');
    }

//     public function verify($token) {
// //        $user = User::where('remember_token', '=', $token)->first();
//         $user = $this->userRepository->getUserByToken($token);

//         if(!empty($user)) {
//             $user->email_verified_at = date('Y-m-d H:i:s');
//             $user->remember_token = Str::random(40);
//             $user->save();

//             return redirect()->route('client.login')
//                 ->with('success', 'Bạn đã kích hoạt tài khoản thành công. Bây giờ có thể đăng nhập')
//                 ->with('msg', 'Bạn đã kích hoạt tài khoản thành công. Bây giờ có thể đăng nhập')
//                 ->with('type', 'success');
//         }else {
//             abort(404);
//         }

//     }

    public function forgot() {

        return view('client.auth.forgot');
    }

    public function handle_forgot(Request $request) {
        $request->validate([
           'email' => 'required|email|max:255'
        ],
        [
            'email.required' => 'Email không để trống',
            'email.email' => 'Email không đúng định dạng',
            'email.max' => 'Email tối đa 255 ký tự',
        ]
        );

        $user = $this->userRepository->getUserByEmail($request->email);

        if(!empty($user)) {
            $user->remember_token = Str::random(40);
            $user->save();

            Mail::to($user->email)->send(new ForgorPasswordMail($user));

            return redirect()->back()
                ->with('success', 'Chúng tôi đã gủi 1 email xác nhận đến bạn. Vui lòng kiểm tra')
                ->with('msg-success', 'Chúng tôi đã gủi 1 email xác nhận đến bạn. Vui lòng kiểm tra');
        }else {
            return redirect()->back()
                ->with('error', 'Email không tồn tại vui lòng kiểm tra lại')
                ->with('msg-error', 'Email không tồn tại vui lòng kiểm tra lại');
        }

    }

    public function reset_password($token)
    {
        $user = $this->userRepository->getUserByToken($token);

        if(!empty($user)) {
            $data['user'] = $user;

            return view('client.auth.reset-password', $data);
        }else {
            abort(404);
        }
    }

    public function handle_reset_password($token, ResetPasswordRequest $request)
    {
        $user = $this->userRepository->getUserByToken($token);

        if(!empty($user)) {
            $user->password = Hash::make($request->password);

            if(empty($user->email_verified_at)) {
                $user->email_verified_at = date('Y-m-d H:i:s');
            }

            $user->remember_token = Str::random(40);
            $user->save();

            return redirect()->route('client.login')
                ->with('success', 'Thay đổi mật khẩu thành công.')
                ->with('msg-success', 'Thay đổi mật khẩu thành công');

        }else {
            abort(404);
        }
    }
}
