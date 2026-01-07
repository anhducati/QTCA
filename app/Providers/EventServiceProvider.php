<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// ✅ Auth events
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Registered;

// ✅ Listeners của bạn
use App\Listeners\LoginSuccessListener;
use App\Listeners\LoginFailedListener;

use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        Login::class => [
            LoginSuccessListener::class,
        ],

        Failed::class => [
            LoginFailedListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
