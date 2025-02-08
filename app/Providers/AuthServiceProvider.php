<?php

namespace App\Providers;

use App\Models\HR\Company;
use App\Models\Spatie\Permission;
use App\Models\Spatie\Permissions;
use App\Models\Spatie\Role;
use App\Policies\HR\CompanyPolicy;
use App\Policies\Spatie\PermissionPolicy;
use App\Policies\Spatie\PermissionsPolicy;
use App\Policies\Spatie\RolePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Die Policy-Zuordnungen für das Application Model.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Hier normalerweise nur Bindings oder Singletons in den Container legen, falls du was brauchst.
        // Ansonsten kann das leer bleiben.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

    }
}
