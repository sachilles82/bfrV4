<?php

namespace App\Providers;

use App\Enums\User\UserType;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
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
        // Spezifisches Binding für Employee-Routes
        Route::bind('user', function (string $value) {
            return once(function () use ($value) {
                return User::where('url_slug', $value)
                    ->where('user_type', UserType::Employee)
                    ->firstOrFail();
            });
        });

//        // Standard User Binding (ohne UserType Filter)
//        Route::bind('user', function (string $value) {
//            return once(function () use ($value) {
//                return User::where('url_slug', $value)
//                    ->firstOrFail();
//            });
//        });
    }
}
