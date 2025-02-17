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

    /** Address */
    case LIST_ADDRESS = 'list-address';
    case CREATE_ADDRESS = 'create-address';
    case EDIT_ADDRESS = 'edit-address';
    case EDIT_OWN_ADDRESS = 'edit-own-address';
    case DELETE_ADDRESS = 'delete-address';

    case LIST_STATE = 'list-state';
    case CREATE_STATE = 'create-state';
    case EDIT_ALL_STATE = 'edit-all-state';
    case EDIT_OWN_STATE = 'edit-own-state';
    case DELETE_ALL_STATE = 'delete-state';
    case DELETE_OWN_STATE = 'delete-own-state';

    case LIST_CITY = 'list-city';
    case CREATE_CITY = 'create-city';
    case EDIT_ALL_CITY = 'edit-all_city';
    case EDIT_OWN_CITY = 'edit-own-city';
    case DELETE_ALL_CITY = 'delete-city';
    case DELETE_OWN_CITY = 'delete-own-city';


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
            Permission::EDIT_BOOKING,
            Permission::DELETE_BOOKING
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

            // Settings (Roles, Address, State)
            Permission::LIST_ROLE,
            Permission::CREATE_ROLE,
            Permission::EDIT_ROLE,
            Permission::EDIT_OWN_ROLE,
            Permission::DELETE_ROLE,
            Permission::DELETE_OWN_ROLE,
            Permission::UPDATE_ROLE_PERMISSIONS,
            Permission::UPDATE_OWN_ROLE_PERMISSIONS,

            Permission::LIST_ADDRESS,
            Permission::CREATE_ADDRESS,
            Permission::EDIT_ADDRESS,
            Permission::EDIT_OWN_ADDRESS,
            Permission::DELETE_ADDRESS,

            Permission::LIST_STATE,
            Permission::CREATE_STATE,
            Permission::EDIT_ALL_STATE,
            Permission::EDIT_OWN_STATE,
            Permission::DELETE_ALL_STATE,
            Permission::DELETE_OWN_STATE,

            Permission::LIST_CITY,
            Permission::CREATE_CITY,
            Permission::EDIT_ALL_CITY,
            Permission::EDIT_OWN_CITY,
            Permission::DELETE_ALL_CITY,
            Permission::DELETE_OWN_CITY,
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
            Permission::EDIT_BOOKING,
            Permission::DELETE_BOOKING
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

            // Settings: Roles
            Permission::LIST_ROLE,
            Permission::CREATE_ROLE,
            Permission::EDIT_ROLE,
            Permission::EDIT_OWN_ROLE,
            Permission::DELETE_ROLE,
            Permission::DELETE_OWN_ROLE,
            Permission::UPDATE_ROLE_PERMISSIONS,
            Permission::UPDATE_OWN_ROLE_PERMISSIONS
            => 'role',

            // Address
            Permission::LIST_ADDRESS,
            Permission::CREATE_ADDRESS,
            Permission::EDIT_ADDRESS,
            Permission::EDIT_OWN_ADDRESS,
            Permission::DELETE_ADDRESS
            => 'address',

            // State (alle Cases abdecken!)
            Permission::LIST_STATE,
            Permission::CREATE_STATE,
            Permission::EDIT_ALL_STATE,
            Permission::EDIT_OWN_STATE,
            Permission::DELETE_ALL_STATE,
            Permission::DELETE_OWN_STATE
            => 'state',

            // City (alle Cases abdecken!)
            Permission::LIST_CITY,
            Permission::CREATE_CITY,
            Permission::EDIT_ALL_CITY,
            Permission::EDIT_OWN_CITY,
            Permission::DELETE_ALL_CITY,
            Permission::DELETE_OWN_CITY
            => 'city',
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

            // Address
            self::LIST_ADDRESS => __('permissions.list-address_description'),
            self::CREATE_ADDRESS => __('permissions.create-address_description'),
            self::EDIT_ADDRESS   => __('permissions.edit-address_description'),
            self::EDIT_OWN_ADDRESS => __('permissions.edit-own-address_description'),
            self::DELETE_ADDRESS => __('permissions.delete-address_description'),

            //State
            self::LIST_STATE => __('permissions.list-state_description'),
            self::CREATE_STATE => __('permissions.create-state_description'),
            self::EDIT_ALL_STATE   => __('permissions.edit-all-state_description'),
            self::EDIT_OWN_STATE => __('permissions.edit-own-state_description'),
            self::DELETE_ALL_STATE => __('permissions.delete-state_description'),
            self::DELETE_OWN_STATE => __('permissions.delete-own-state_description'),

            //City
            self::LIST_CITY => __('permissions.list-city_description'),
            self::CREATE_CITY => __('permissions.create-city_description'),
            self::EDIT_ALL_CITY   => __('permissions.edit-all-city_description'),
            self::EDIT_OWN_CITY => __('permissions.edit-own-city_description'),
            self::DELETE_ALL_CITY => __('permissions.delete-city_description'),
            self::DELETE_OWN_CITY => __('permissions.delete-own-city_description'),

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
