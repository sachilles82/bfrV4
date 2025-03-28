<?php

namespace Database\Seeders\Spatie;

namespace Database\Seeders\Spatie;

use App\Enums\Role\Permission;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\UserHasRole;
use App\Enums\Role\RoleVisibility;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Erstelle die Super Admin Rolle
        $superAdmin = Role::create([
            'name' => UserHasRole::SuperAdmin,
            'created_by' => 1,
            'visible' => RoleVisibility::HiddenInNova,
            'access' => RoleHasAccessTo::AdminPanel,
            'description' => __('Super Admin mit allen Berechtigungen'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => true
        ]);

        // Der Super Admin erhält alle Permissions (aus dem Model, hier nicht aus dem Enum)
        $permissions = \Spatie\Permission\Models\Permission::pluck('id', 'id')->all();
        $superAdmin->syncPermissions($permissions);

        // Erstelle weitere Rollen (Admin, Support, Marketing, Sales, ...)
        Role::create([
            'access' => RoleHasAccessTo::AdminPanel,
            'name' => UserHasRole::Admin,
            'created_by' => 1,
            'visible' => RoleVisibility::HiddenInNova,
            'description' => __('Admin Role als Standart für Admins damit sie sich in Nova anmelden können'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'access' => RoleHasAccessTo::AdminPanel,
            'name' => UserHasRole::Support,
            'created_by' => 1,
            'visible' => RoleVisibility::VisibleInNova,
            'description' => __('Support Team Role für Nova Admin User'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'access' => RoleHasAccessTo::AdminPanel,
            'name' => UserHasRole::Marketing,
            'created_by' => 1,
            'visible' => RoleVisibility::VisibleInNova,
            'description' => __('Marketing Team Role für Nova Admin User'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'access' => RoleHasAccessTo::AdminPanel,
            'name' => UserHasRole::Sales,
            'created_by' => 1,
            'visible' => RoleVisibility::VisibleInNova,
            'description' => __('Sales Team Role für Nova Admin User'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        // Erstelle die Owner-Rolle und weise ihr Permissions zu
        $ownerRole = Role::create([
            'access' => RoleHasAccessTo::OwnerPanel,
            'name' => UserHasRole::Owner,
            'created_by' => 1,
            'visible' => RoleVisibility::Hidden,
            'description' => __('Default Owner Role with full Permissions for Owner Panel/Customer App user'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => true
        ]);

        // Hole alle Permissions, die der "settingApp" zugeordnet sind (aus dem Enum)
        $permissionsForSettingApp = Permission::casesForApp('settingApp');

        // Transformiere die Enum-Cases in ein Array von String-Werten
        $permissionValues = array_map(fn($perm) => $perm->value, $permissionsForSettingApp);

        // Weise der Owner-Rolle diese Permissions zu
        $ownerRole->syncPermissions($permissionValues);

        // Erstelle weitere Owner-Rollen
        Role::create([
            'access' => RoleHasAccessTo::OwnerPanel,
            'name' => UserHasRole::Owner1,
            'created_by' => 1,
            'visible' => RoleVisibility::Hidden,
            'description' => __('Owner Role1 with limited Access to Owner Panel'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => true
        ]);

        Role::create([
            'access' => RoleHasAccessTo::OwnerPanel,
            'name' => UserHasRole::Owner2,
            'created_by' => 1,
            'visible' => RoleVisibility::Hidden,
            'description' => __('Owner Role2 with limited Access to Owner Panel'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => true
        ]);

        Role::create([
            'access' => RoleHasAccessTo::OwnerPanel,
            'name' => UserHasRole::Owner3,
            'created_by' => 1,
            'visible' => RoleVisibility::Hidden,
            'description' => __('Owner Role3 with limited Access to Owner Panel'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => true
        ]);

        // Employee Rollen erstellen
        Role::create([
            'name' => UserHasRole::Employee,
            'access' => RoleHasAccessTo::EmployeePanel,
            'visible' => RoleVisibility::Hidden,
            'description' => __('Default Employee Role for Employee Panel Access only'),
            'created_by' => 1,
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'name' => UserHasRole::Worker,
            'access' => RoleHasAccessTo::EmployeePanel,
            'visible' => RoleVisibility::Visible,
            'description' => __('Worker_desc'),
            'created_by' => 1,
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'name' => UserHasRole::Manager,
            'access' => RoleHasAccessTo::EmployeePanel,
            'visible' => RoleVisibility::Visible,
            'description' => __('Manager_desc'),
            'created_by' => 1,
            'company_id' => null,
            'team_id' => null,
            'is_manager' => true
        ]);

        Role::create([
            'name' => UserHasRole::Editor,
            'access' => RoleHasAccessTo::EmployeePanel,
            'visible' => RoleVisibility::Visible,
            'description' => __('Editor_desc'),
            'created_by' => 1,
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'name' => UserHasRole::Temporary,
            'access' => RoleHasAccessTo::EmployeePanel,
            'visible' => RoleVisibility::Visible,
            'description' => __('Temporary_desc'),
            'created_by' => 1,
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        // Partner Rollen erstellen
        Role::create([
            'access' => RoleHasAccessTo::PartnerPanel,
            'name' => UserHasRole::Partner,
            'created_by' => 1,
            'visible' => RoleVisibility::Hidden,
            'description' => __('Partner_desc'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'access' => RoleHasAccessTo::PartnerPanel,
            'name' => UserHasRole::Partner1,
            'created_by' => 1,
            'visible' => RoleVisibility::Visible,
            'description' => __('Pieceworker Role for Partner Panel Access'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'access' => RoleHasAccessTo::PartnerPanel,
            'name' => UserHasRole::Partner2,
            'created_by' => 1,
            'visible' => RoleVisibility::Visible,
            'description' => __('Subcontractor Role for Partner Panel Access'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'access' => RoleHasAccessTo::PartnerPanel,
            'name' => UserHasRole::Partner3,
            'created_by' => 1,
            'visible' => RoleVisibility::Visible,
            'description' => __('Supplier Role for Partner Panel Access'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'access' => RoleHasAccessTo::PartnerPanel,
            'name' => UserHasRole::Partner4,
            'created_by' => 1,
            'visible' => RoleVisibility::Visible,
            'description' => __('Client Role for Partner Panel Access'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);

        Role::create([
            'access' => RoleHasAccessTo::PartnerPanel,
            'name' => UserHasRole::Partner5,
            'created_by' => 1,
            'visible' => RoleVisibility::Visible,
            'description' => __('Builder Role for Partner Panel Access'),
            'company_id' => null,
            'team_id' => null,
            'is_manager' => false
        ]);
    }
}
