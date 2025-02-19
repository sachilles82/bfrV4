<?php

namespace App\Enums\User;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';

    public static function options(): array
    {
        return [
            self::Male->value   => 'Male',
            self::Female->value => 'Female',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Male   => __('Male'),
            self::Female => __('Female'),
        };
    }
}
