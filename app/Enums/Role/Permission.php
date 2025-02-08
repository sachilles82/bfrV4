<?php

namespace App\Enums\Role;

enum Permission: string
{
    /**
     * Gibt nur die Enum‑Cases zurück, die zur angegebenen App gehören.
     */
    public static function casesForApp(string $app): array
    {
        return array_values(array_filter(
            self::cases(),
            fn($perm) => $perm->appName() === $app
        ));
    }

    //---- BASE APP ----

    /** Teams */
    case LIST_TEAM = 'list-team';
    case CREATE_TEAM = 'create-team';
    case EDIT_TEAM = 'edit-team';
    case DELETE_TEAM = 'delete-team';

    /** Company */
    case LIST_COMPANY = 'list-company';
    case CREATE_COMPANY = 'create-company';
    case EDIT_COMPANY = 'edit-company';
    case DELETE_COMPANY = 'delete-company';

    case LIST_USER = 'list-user';
    case CREATE_USER = 'create-user';
    case EDIT_USER = 'edit-user';
    case DELETE_USER = 'delete-user';

    // ---- HOLIDAY APP ----
    case LIST_BOOKING = 'list-booking';
    case CREATE_BOOKING = 'create-booking';
    case EDIT_BOOKING = 'edit-booking';
    case DELETE_BOOKING = 'delete-booking';
    // ... etc.

    // ---- CRM APP ----
    case LIST_CUSTOMER = 'list-customer';
    case CREATE_CUSTOMER = 'create-customer';
    case EDIT_CUSTOMER = 'edit-customer';
    case DELETE_CUSTOMER = 'delete-customer';
    // ... etc.

    // ---- Project APP ----
    case LIST_PROJECT = 'list-project';
    case CREATE_PROJECT = 'create-project';
    case EDIT_PROJECT = 'edit-project';
    case DELETE_PROJECT = 'delete-project';
    // ... etc.

    /** Settings */
    // ---- Roles and Permissions ----
    case LIST_ROLE = 'list-role';
    case CREATE_ROLE = 'create-role';
    case EDIT_ROLE = 'edit-role';
    case EDIT_OWN_ROLE = 'edit-own-role';
    case DELETE_ROLE = 'delete-role';
    case DELETE_OWN_ROLE = 'delete-own-role';
    case UPDATE_ROLE_PERMISSIONS = 'update-role-permissions';
    case UPDATE_OWN_ROLE_PERMISSIONS = 'update-own-role-permissions';
    // ... etc.


    /**
     * Bestimmt, welcher App dieser enum-Wert zugeordnet ist.
     */
    public function appName(): string
    {
        return match ($this) {
            // BASE APP
            Permission::LIST_TEAM,
            Permission::CREATE_TEAM,
            Permission::EDIT_TEAM,
            Permission::DELETE_TEAM,

            Permission::LIST_COMPANY,
            Permission::CREATE_COMPANY,
            Permission::EDIT_COMPANY,
            Permission::DELETE_COMPANY,

            Permission::LIST_USER,
            Permission::CREATE_USER,
            Permission::EDIT_USER,
            Permission::DELETE_USER
            => 'baseApp',

            // HOLIDAY APP
            Permission::LIST_BOOKING,
            Permission::CREATE_BOOKING,
            PERMISSION::EDIT_BOOKING,
            PERMISSION::DELETE_BOOKING
            => 'holidayApp',

            // CRM APP
            Permission::LIST_CUSTOMER,
            Permission::CREATE_CUSTOMER,
            Permission::EDIT_CUSTOMER,
            Permission::DELETE_CUSTOMER
            => 'crmApp',

            // Project APP
            Permission::LIST_PROJECT,
            Permission::CREATE_PROJECT,
            Permission::EDIT_PROJECT,
            Permission::DELETE_PROJECT
            => 'projectApp',

            // Settings
            Permission::LIST_ROLE,
            Permission::CREATE_ROLE,
            Permission::EDIT_ROLE,
            Permission::EDIT_OWN_ROLE,
            Permission::DELETE_ROLE,
            Permission::DELETE_OWN_ROLE,
            Permission::UPDATE_ROLE_PERMISSIONS,
            Permission::UPDATE_OWN_ROLE_PERMISSIONS
            => 'settingApp',
        };
    }

    /**
     * Bestimmt die (feinere) Gruppierung innerhalb einer App.
     * Du kannst hier flexibel definieren, wie du gruppieren willst.
     */
    public function group(): string
    {
        return match ($this) {
            // BASE APP
            Permission::LIST_TEAM,
            Permission::CREATE_TEAM,
            Permission::EDIT_TEAM,
            Permission::DELETE_TEAM
            => 'team',

            Permission::LIST_COMPANY,
            Permission::CREATE_COMPANY,
            Permission::EDIT_COMPANY,
            Permission::DELETE_COMPANY
            => 'company',

            Permission::LIST_USER,
            Permission::CREATE_USER,
            Permission::EDIT_USER,
            Permission::DELETE_USER
            => 'user',

            // HOLIDAY APP
            Permission::LIST_BOOKING,
            Permission::CREATE_BOOKING,
            PERMISSION::EDIT_BOOKING,
            PERMISSION::DELETE_BOOKING
            => 'booking',

            // CRM APP
            Permission::LIST_CUSTOMER,
            Permission::CREATE_CUSTOMER,
            Permission::EDIT_CUSTOMER,
            Permission::DELETE_CUSTOMER
            => 'customer',

            // Project APP
            Permission::LIST_PROJECT,
            Permission::CREATE_PROJECT,
            Permission::EDIT_PROJECT,
            Permission::DELETE_PROJECT
            => 'project',

            // Settings
            Permission::LIST_ROLE,
            Permission::CREATE_ROLE,
            Permission::EDIT_ROLE,
            Permission::EDIT_OWN_ROLE,
            Permission::DELETE_ROLE,
            Permission::DELETE_OWN_ROLE,
            Permission::UPDATE_ROLE_PERMISSIONS,
            Permission::UPDATE_OWN_ROLE_PERMISSIONS
            => 'role',
        };
    }

    /**
     * Liefert eine Beschreibung der Berechtigung.
     */
    public function description(): string
    {
        return match ($this) {
            // BASE APP: Teams
            self::LIST_TEAM => __('permissions.list-team_description'),
            self::CREATE_TEAM => __('permissions.create-team_description'),
            self::EDIT_TEAM   => __('permissions.edit-team_description'),
            self::DELETE_TEAM => __('permissions.delete-team_description'),

            // BASE APP: Company
            self::LIST_COMPANY => __('permissions.list-company_description'),
            self::CREATE_COMPANY => __('permissions.create-company_description'),
            self::EDIT_COMPANY   => __('permissions.edit-company_description'),
            self::DELETE_COMPANY => __('permissions.delete-company_description'),

            // BASE APP: User
            self::LIST_USER => __('permissions.list-user_description'),
            self::CREATE_USER => __('permissions.create-user_description'),
            self::EDIT_USER   => __('permissions.edit-user_description'),
            self::DELETE_USER => __('permissions.delete-user_description'),

            // HOLIDAY APP: Booking
            self::LIST_BOOKING => __('permissions.list-booking_description'),
            self::CREATE_BOOKING => __('permissions.create-booking_description'),
            self::EDIT_BOOKING   => __('permissions.edit-booking_description'),
            self::DELETE_BOOKING => __('permissions.delete-booking_description'),

            // CRM APP: Customer
            self::LIST_CUSTOMER => __('permissions.list-customer_description'),
            self::CREATE_CUSTOMER => __('permissions.create-customer_description'),
            self::EDIT_CUSTOMER   => __('permissions.edit-customer_description'),
            self::DELETE_CUSTOMER => __('permissions.delete-customer_description'),

            // Project APP: Project
            self::LIST_PROJECT => __('permissions.list-project_description'),
            self::CREATE_PROJECT => __('permissions.create-project_description'),
            self::EDIT_PROJECT   => __('permissions.edit-project_description'),
            self::DELETE_PROJECT => __('permissions.delete-project_description'),

            // Settings: Roles
            self::LIST_ROLE => __('permissions.list-role_description'),
            self::CREATE_ROLE => __('permissions.create-role_description'),
            self::EDIT_ROLE   => __('permissions.edit-role_description'),
            self::EDIT_OWN_ROLE => __('permissions.edit-own-role_description'),
            self::DELETE_ROLE => __('permissions.delete-role_description'),
            self::DELETE_OWN_ROLE => __('permissions.delete-own-role_description'),
            self::UPDATE_ROLE_PERMISSIONS => __('permissions.update-role-permissions_description'),
            self::UPDATE_OWN_ROLE_PERMISSIONS => __('permissions.update-own-role-permissions_description'),
        };
    }

    /**
     * Liefert das Label (kurzer Name) der Berechtigung, lokalisiert.
     */
    public function label(): string
    {
        // Beispiel: "permissions.list-team_label"
        return __("permissions.{$this->value}_label");
    }
}
