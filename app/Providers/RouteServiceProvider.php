<?php

namespace App\Providers;

use App\Enums\User\UserType;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Route::bind('employee', function (string $value) {
            return User::where('slug', $value)
                ->where('user_type', UserType::Employee)
                ->firstOrFail();
        });
    }
}
