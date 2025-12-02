<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;

use App\Repositories\BlogRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\Interfaces\BlogInterface;
use App\Repositories\Interfaces\CategoryInterface;
use App\Repositories\Interfaces\UserInterface;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $serviceBindings = [
        'App\Services\Interfaces\UserServiceInterface' => 'App\Services\UserService',
        'App\Repositories\Interfaces\UserInterface' => 'App\Repositories\UserRepository',
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->serviceBindings as $key => $value) {
            $this->app->bind($key, $value);
        }

        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
        $this->app->bind(BlogInterface::class, BlogRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
              // Directive: @canModule('brands','create') ... @endcanModule
        Blade::if('canModule', function (string $module, string $action = 'read') {
            $user = auth()->user();
            if (!$user) {
                return false;
            }

            // Hàm canModule() đã viết trong User model
            return $user->canModule($module, $action);
        });
    }
}
