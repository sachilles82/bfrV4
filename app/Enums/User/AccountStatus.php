<?php

namespace App\Enums\User;

enum AccountStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';
    case TRASHED = 'trashed';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive'),
            self::ARCHIVED => __('Archived'),
            self::TRASHED => __('In Trash'),
        };
    }

    public function colors(): string
    {
        return match ($this) {
            static::ACTIVE => ' dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20 ring-green-600/20 text-green-700 bg-green-50',
            static::INACTIVE => 'dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20 ring-yellow-600/20 text-yellow-800 bg-yellow-50',
            static::TRASHED => 'dark:bg-red-400/10 dark:ring-red-400/20 dark:text-red-400 text-red-700 ring-red-600/10 bg-red-50',
            static::ARCHIVED => 'dark:bg-gray-400/10 dark:ring-gray-400/20 dark:text-gray-400 text-gray-600 ring-gray-500/10 bg-gray-50',
//            default => 'dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20 ring-yellow-600/20 text-yellow-800 bg-yellow-50',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            static::ACTIVE => 'icon.check',
            static::INACTIVE => 'icon.clock',
            static::ARCHIVED => 'icon.archive-box',
            static::TRASHED => 'icon.trash',
        };
    }

}
