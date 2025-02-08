<?php

namespace App\Enums\User;

enum UserType: string
{
    case Admin = 'admin';
    case Owner = 'owner';
    case Employee = 'employee';
    case Partner = 'partner';

    public function label(): string
    {
        return match ($this) {
            static::Admin => __('Admin'),
            static::Owner => __('Owner'),
            static::Employee => __('Employee'),
            static::Partner => __('Partner'),
        };
    }
}

