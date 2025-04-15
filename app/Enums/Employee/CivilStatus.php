<?php

namespace App\Enums\Employee;

enum CivilStatus: string
{
    case Single = 'single';
    case Married = 'married';
    case Widowed = 'widowed';
    case Divorced = 'divorced';
    case Separated = 'separated';
    case RegisteredPartnership = 'registered_partnership';
    case DissolvedPartnership = 'dissolved_partnership';

    public static function options(): array
    {
        return [
            self::Single->value => 'Single',
            self::Married->value => 'Married',
            self::Widowed->value => 'Widowed',
            self::Divorced->value => 'Divorced',
            self::Separated->value => 'Separated',
            self::RegisteredPartnership->value => 'In a registered partnership',
            self::DissolvedPartnership->value => 'In a dissolved partnership',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Single => 'Single',
            self::Married => 'Married',
            self::Widowed => 'Widowed',
            self::Divorced => 'Divorced',
            self::Separated => 'Separated',
            self::RegisteredPartnership => 'In a registered partnership',
            self::DissolvedPartnership => 'In a dissolved partnership',
        };
    }
}
