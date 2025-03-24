<?php

namespace App\Enums\Employee;

enum Residence: string
{
    case S = 'Swiss';
    case C = 'C';
    case B = 'B';
    case B1 = 'B1';
    case L = 'L';
    case L1 = 'L1';
    case F = 'F';

    public static function options(): array
    {
        return [
            self::S->value => 'Swiss',
            self::C->value => 'Residence C',
            self::B->value => 'Residence B',
            self::B1->value => 'Residence B1 - EU/EFTA',
            self::L->value => 'Residence L',
            self::L1->value => 'Residence L1 - EU/EFTA',
            self::F->value => 'Other',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::S => 'Swiss Citizen',
            self::C => 'Residence C',
            self::B => 'Residence B',
            self::B1 => 'Residence B1 - EU/EFTA',
            self::L => 'Residence L',
            self::L1 => 'Residence L1 - EU/EFTA',
            self::F => 'Other',

        };
    }
}
