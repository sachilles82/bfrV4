<?php

namespace App\Enums\User;

enum EmployeeAccountStatus: string
{
    case ACTIVE = 'active';
    case NOT_ACTIVATED = 'not_activated';
    case ARCHIVED = 'archived';
    case TRASHED = 'trashed';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => __('Active'),
            self::NOT_ACTIVATED => __('Not Activated'),
            self::ARCHIVED => __('Archived'),
            self::TRASHED => __('In Trash'),
        };
    }

    public function fullColorClasses(): string
    {
        return match($this->value) {
            'active'         => 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 ring-green-600/20 dark:ring-green-500/20',
            'not_activated'  => 'bg-yellow-50 dark:bg-yellow-400/10 text-yellow-800 dark:text-yellow-500 ring-yellow-600/20 dark:ring-yellow-400/20',
            'archived'       => 'bg-gray-50 dark:bg-gray-400/10 text-gray-600 dark:text-gray-400 ring-gray-500/10 dark:ring-gray-400/20',
            'trashed'        => 'bg-red-50 dark:bg-red-400/10 text-red-700 dark:text-red-400 ring-red-600/10 dark:ring-red-400/20',
        };
    }


}
