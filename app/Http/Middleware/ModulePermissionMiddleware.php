<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ModulePermissionMiddleware
{
    public function handle(Request $request, Closure $next, $module, $action)
    {
        $user = auth()->user();

        // Nếu chưa đăng nhập → chặn
        if (!$user) {
            return redirect()->route('admin.login')
                ->with('msg-error', 'Bạn cần đăng nhập để tiếp tục');
        }

        // Admin T1 → full quyền
        if ((int) $user->is_admin === 1) {
            return $next($request);
        }

        // User không có quyền theo module
        if (!$user->canModule($module, $action)) {

            // Nếu request là DELETE hoặc PUT (form method spoofing)
            if ($request->method() !== 'GET') {
                return redirect()->back()->with('msg-error', 'Bạn không có quyền thực hiện thao tác này');
            }

            // Nếu request là GET
            return redirect()->back()->with('msg-error', 'Bạn không có quyền truy cập chức năng này');
        }

        return $next($request);
    }
}
