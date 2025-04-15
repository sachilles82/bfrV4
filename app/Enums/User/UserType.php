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
            self::Admin => __('Admin'), // User ist Nova Admin, mein Mitarbeiter
            self::Owner => __('Owner'), // User ist der Besitzer der Company, mein Kunde
            self::Employee => __('Employee'), // User ist ein Mitarbeiter der Company,
            self::Partner => __('Partner'),// User ist ein Partner der Company
        };
    }
}
