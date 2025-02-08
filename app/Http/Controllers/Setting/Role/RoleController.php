<?php

namespace App\Http\Controllers\Setting\Role;

use App\Enums\Role\Permission;
use App\Http\Controllers\Controller;
use App\Models\Spatie\Role;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    public function show(int $roleId, string $app)
    {
        Gate::authorize(Permission::LIST_ROLE);

        $role = Role::findOrFail($roleId);

        abort_unless(in_array($app,
            [
                'baseApp',
                'crmApp',
                'holidayApp',
                'projectApp',
                'settingApp',
            ]
        ), 404);

        return view('laravel.setting.role.show',
            compact('role', 'app')
        );
    }
}
