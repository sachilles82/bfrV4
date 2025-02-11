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
            static::Admin => __('Admin'), // User ist Nova Admin, mein Mitarbeiter
            static::Owner => __('Owner'), // User ist der Besitzer der Company, mein Kunde
            static::Employee => __('Employee'), // User ist ein Mitarbeiter der Company,
            static::Partner => __('Partner'),// User ist ein Partner der Company
        };
    }
}

