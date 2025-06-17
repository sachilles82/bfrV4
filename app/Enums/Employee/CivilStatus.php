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
            self::Single->value => __('Single'),
            self::Married->value => __('Married'),
            self::Widowed->value => __('Widowed'),
            self::Divorced->value => __('Divorced'),
            self::Separated->value => __('Separated'),
            self::RegisteredPartnership->value => __('In a registered partnership'),
            self::DissolvedPartnership->value => __('In a dissolved partnership'),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Single => __('Single'),
            self::Married => __('Married'),
            self::Widowed => __('Widowed'),
            self::Divorced => __('Divorced'),
            self::Separated => __('Separated'),
            self::RegisteredPartnership => __('In a registered partnership'),
            self::DissolvedPartnership => __('In a dissolved partnership'),
        };
    }

    public static function getCivilOptions(): array
    {
        return collect(self::cases())
            ->map(function (CivilStatus $status) {
                return [
                    'value' => $status->value,
                    'label' => $status->label(),
                ];
            })
            ->toArray();
    }
}
