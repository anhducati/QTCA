<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\UserRequest;
use App\Mail\RegisterMail;
use App\Models\User;
use App\Models\ModulePermission; // <-- THÊM DÒNG NÀY
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\UserInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Requests\Backend\ChangePasswordRequest;
use App\Http\Requests\ChangePasswordRequest as RequestsChangePasswordRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct(UserInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        session()->flash('active', 'user');

        $users = $this->userRepository->getRecordUser();

        return view('backend.user.index', compact(
            'users',
        ));
    }

    public function create_user()
    {
        return view('backend.user.create');
    }

    public function handle_create_user(UserRequest $request)
    {
        $save = new User();
        $save->name = trim($request->name);
        $save->email = trim($request->email);

        // Mật khẩu mặc định
        $defaultPassword = '123456';
        $save->password = Hash::make($defaultPassword);

        $save->remember_token = Str::random(40);
        $save->is_admin = $request->is_admin;
        $save->status = $request->status;
        $save->email_verified_at = now();
        $save->save();

        // Nếu muốn set quyền mặc định có thể thêm ở đây (ví dụ cho T2/T3)

        return redirect()->route('admin.user.index')
            ->with('msg-success', 'Đã thêm thành công một tài khoản. Mật khẩu mặc định là 123456');
    }

    public function update_user($id)
    {
        $user = $this->userRepository->getUserById($id);

        // load luôn modulePermissions để view dùng
        if ($user instanceof User) {
            $user->load('modulePermissions');
        }

        $data = [
            'user' => $user,
        ];

        return view('backend.user.update', $data);
    }

    public function handle_update_user(UserRequest $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $this->userRepository->getUserById($id);

        if (!$user) {
            return redirect()->route('admin.user.index')
                ->with('msg-error', 'Không tìm thấy tài khoản.');
        }

        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->is_admin = $request->is_admin;
        $user->status = $request->status;
        $user->updated_at = now();

        $changedPassword = false;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
            $changedPassword = true;
        }

        $user->save();

        /**
         * ==============================
         *  CẬP NHẬT PHÂN QUYỀN MODULE
         * ==============================
         *
         * Form gửi lên với dạng:
         * permissions[brands][read]   = 1
         * permissions[brands][create] = 1
         * ...
         */
        $permissions = $request->input('permissions', []);

        foreach ($permissions as $moduleKey => $acts) {
            $perm = ModulePermission::firstOrNew([
                'user_id'    => $user->id,
                'module_key' => $moduleKey,
            ]);

            $perm->can_read   = !empty($acts['read']);
            $perm->can_create = !empty($acts['create']);
            $perm->can_update = !empty($acts['update']);
            $perm->can_delete = !empty($acts['delete']);
            $perm->save();
        }

        // Xóa những module không còn trong request => coi như bỏ hết quyền
        if (!empty($permissions)) {
            $moduleKeys = array_keys($permissions);
            ModulePermission::where('user_id', $user->id)
                ->whereNotIn('module_key', $moduleKeys)
                ->delete();
        } else {
            // Nếu không gửi gì lên -> xóa sạch quyền
            ModulePermission::where('user_id', $user->id)->delete();
        }

        // Thông báo
        if ($changedPassword) {
            return redirect()->route('admin.user.update', $id)
                ->with('msg-success', 'Cập nhật thông tin, mật khẩu và phân quyền thành công');
        }

        return redirect()->route('admin.user.update', $id)
            ->with('msg-success', 'Cập nhật thông tin & phân quyền thành công');
    }

    public function delete_user($id)
    {
        $user = $this->userRepository->getUserById($id);

        if (!$user) {
            return redirect()->route('admin.user.index')->with('msg-error', "Không tìm thấy người dùng có ID là $id.");
        }

        $user->delete();

        return redirect()->route('admin.user.index')->with('msg-success', "Xóa tài khoản: $user->email thành công.");
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('admin.user.index')
            ->with('msg-success', 'Đổi mật khẩu thành công.');
    }
}
