<?php

namespace App\Enums\User;

enum Gender: string
{
    case Other = 'other';
    case Male = 'male';
    case Female = 'female';

    public function label(): string
    {
        return match ($this) {
            static::Other => __('Other'),
            static::Male => __('Male'),
            static::Female => __('Female'),
        };
    }
}
