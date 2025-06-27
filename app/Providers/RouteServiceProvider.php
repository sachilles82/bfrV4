<?php

namespace App\Providers;

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
        Route::bind('employee', function (string $value) {
            return Cache::remember(
                "employee_profile_{$value}",
                300, // 5 Minuten
                fn() => User::select(['id', 'url_slug', 'name'])
                    ->where('url_slug', $value)
                    ->firstOrFail()
            );
        });
    }
}
