<?php

namespace App\Enums\Employee;

enum EmployeeStatus: string
{
    case ONBOARDING = 'onboarding';
    case PROBATION = 'probation';
    case EMPLOYED = 'employed';
    case ONLEAVE = 'onleave';
    case LEAVE = 'leave';

    public function label(): string
    {
        return match ($this) {
            self::ONBOARDING => __('onboarding'),
            self::PROBATION => __('probation'),
            self::EMPLOYED => __('employed'),
            self::ONLEAVE => __('onleave'),
            self::LEAVE => __('leave'),
        };
    }

    public function colors(): string
    {
        return match ($this) {
            static::ONBOARDING => ' dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20 ring-blue-600/20 text-blue-700 bg-blue-50',
            static::PROBATION => 'dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20 ring-yellow-600/20 text-yellow-800 bg-yellow-50',
            static::EMPLOYED => ' dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20 ring-green-600/20 text-green-700 bg-green-50',
            static::ONLEAVE => 'dark:bg-gray-400/10 dark:text-gray-500 dark:ring-gray-400/20 ring-gray-600/20 text-gray-800 bg-gray-50',
            default => 'dark:bg-red-400/10 dark:ring-red-400/20 dark:text-red-400 text-red-700 ring-red-600/10 bg-red-50',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            static::ONBOARDING => 'icon.check',
            static::PROBATION => 'icon.archive-box',
            static::EMPLOYED => 'icon.check',
            static::ONLEAVE => 'icon.trash',
            static::LEAVE => 'icon.trash',
        };
    }
}
