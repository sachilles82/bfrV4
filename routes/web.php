<?php

use App\Enums\Role\Permission;
use App\Http\Controllers\HR\CompanyController;
use App\Http\Controllers\HR\DepartmentController;
use App\Http\Controllers\HR\Employee\ComplianceController;
use App\Http\Controllers\HR\Employee\ProfileController;
use App\Http\Controllers\Setting\Role\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/cartoon', function () {
    return view('cartoon');
})->name('cartoon');

Route::get('/space', function () {
    return view('space');
})->name('space');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/quiz', function () {
        return view('quiz');
    })->name('quiz');


    Route::prefix('settings')->group(function () {

        // Hier werden nur diese Routen gecached:
        Route::get('/company', [CompanyController::class, 'show'])
//            ->middleware('cacheResponse')
            ->name('settings.company');

        Route::get('/profile', [
            ProfileController::class, 'show'
        ])->name('settings.profile');

        Route::get('/compliance', [
            ComplianceController::class, 'show'
        ])->name('settings.compliance');


        Route::get('/departments', function () {
            return view('laravel/hr/department/index');
        })->name('settings.departments');

        Route::get('/departments/{department}', [
            DepartmentController::class, 'show'
        ])->name('settings.departments.show');

        Route::middleware(['can:' . Permission::LIST_ROLE->value])->group(function () {
            Route::get('/roles', function () {
                return view('laravel/spatie/role/index');
            })->name('settings.roles');

            Route::get('/roles/{roleId}/{app}', [RoleController::class, 'show'])
                ->name('settings.roles.show');
        });


    });
});
