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

    public function dotColor(): string
    {
        return match ($this) {

            static::ACTIVE => 'fill-green-600 dark:fill-green-400/70',
            static::ARCHIVED => 'fill-gray-600 dark:fill-gray-400/70',
            default => 'fill-red-700 dark:fill-red-400',
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
