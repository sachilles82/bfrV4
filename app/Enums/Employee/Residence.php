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
            self::S->value => __('Swiss'),
            self::C->value => __('Residence C'),
            self::B->value => __('Residence B'),
            self::B1->value => __('Residence B1 - EU/EFTA'),
            self::L->value => __('Residence L'),
            self::L1->value => __('Residence L1 - EU/EFTA'),
            self::F->value => __('Other'),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::S => __('Swiss Citizen'),
            self::C => __('Residence C'),
            self::B => __('Residence B'),
            self::B1 => __('Residence B1 - EU/EFTA'),
            self::L => __('Residence L'),
            self::L1 => __('Residence L1 - EU/EFTA'),
            self::F => __('Other'),
        };
    }

    public static function getResidenceOptions(): array
    {
        return collect(self::cases())
            ->map(function (Residence $residence) {
                return [
                    'value' => $residence->value,
                    'label' => $residence->label(),
                ];
            })
            ->toArray();
    }
}
