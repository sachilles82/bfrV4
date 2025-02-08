<?php

namespace Database\Seeders\Spatie;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
use App\Enums\Role\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Permission::cases() as $permissionEnum) {
            PermissionModel::updateOrCreate(
                ['name' => $permissionEnum->value],
                [
                    'group' => $permissionEnum->group(),
                    'app_name' => $permissionEnum->appName(),
                ]
            );
        }
    }
}
