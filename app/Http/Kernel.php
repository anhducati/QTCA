<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [

        // ===============================
        // 1️⃣ HỆ THỐNG GỐC (Laravel)
        // ===============================
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

        // ===============================
        // 2️⃣ CHẶN CỨNG – CHẠY ĐẦU TIÊN
        // ===============================

        // 🚨 PANIC MODE (OFF toàn site)
        \App\Http\Middleware\PanicMiddleware::class,

        // 🧱 BLOCK IP (tay + tự động)
        \App\Http\Middleware\BlockIpMiddleware::class,

        // 🔐 SERVER LOCK (off/on + unlock link)
        \App\Http\Middleware\ServerLockMiddleware::class,

        // ===============================
        // 3️⃣ SECURITY MONITOR (SAU KHI QUA CỬA)
        // ===============================

        // 🤖 Detect bot / tool
        \App\Http\Middleware\DetectBotMiddleware::class,

        // 📊 Đếm traffic
        \App\Http\Middleware\TrafficMonitorMiddleware::class,

        // 🚨 Detect flood / scan / auto block
        \App\Http\Middleware\DetectTraffic::class,
    ];


    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetLocale::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign 
     * middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'check.login' => \App\Http\Middleware\CheckLoginMiddleware::class,

        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        /**
         * ================================
         *  PHÂN QUYỀN MODULE + CRUD
         * ================================
         * Sử dụng trong route:
         * ->middleware('module_permission:brands,read')
         * ->middleware('module_permission:models,create')
         */
        'module_permission' => \App\Http\Middleware\ModulePermissionMiddleware::class,
    ];
}
