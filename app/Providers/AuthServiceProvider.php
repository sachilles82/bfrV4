<?php

namespace App\Providers;

use App\Http\Controllers\Alem\Employee\EmployeeController;
use App\Models\Address\State;
use App\Models\Alem\Company;
use App\Models\Alem\Employee\Employee;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Spatie\Permission;
use App\Models\Spatie\Role;
use App\Policies\Address\AddressablePolicy;
use App\Policies\Address\StatePolicy;
use App\Policies\Alem\CompanyPolicy;
use App\Policies\Alem\Employee\EmployeePolicy;
use App\Policies\Alem\Employee\Setting\ProfessionPolicy;
use App\Policies\Spatie\PermissionPolicy;
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

        Company::class => AddressablePolicy::class,

        //  Address State City
        State::class => StatePolicy::class,
        EmployeeController::class => EmployeePolicy::class,
        Employee::class => EmployeePolicy::class,
        Profession::class => ProfessionPolicy::class,
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
