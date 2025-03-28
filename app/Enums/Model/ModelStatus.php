<?php

namespace App\Enums\Model;

enum ModelStatus: string
{
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
    case TRASHED = 'trashed';

    public function label(): string
    {
        return match($this) {
            self::ACTIVE => __('Active'),
            self::ARCHIVED => __('Archived'),
            self::TRASHED => __('In Trash'),
        };
    }

    public function colors(): string
    {
        return match ($this) {
            static::ACTIVE => 'dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20 ring-green-600/20 text-green-700 bg-green-50 h-5 w-5 rounded-md',
            static::ARCHIVED => 'dark:bg-gray-400/10 dark:text-gray-500 dark:ring-gray-400/20 ring-gray-600/20 text-gray-800 bg-gray-50 h-5 w-5 rounded-md',
            static::TRASHED => 'dark:bg-red-400/10 dark:ring-red-400/20 dark:text-red-400 text-red-700 ring-red-600/10 bg-red-50 h-5 w-5 rounded-md',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            static::ACTIVE => 'icon.check',
            static::ARCHIVED => 'icon.archive-box',
            static::TRASHED => 'icon.trash',
        };
    }
}
