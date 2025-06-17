<?php

namespace App\Enums\Employee;

enum Probation: string
{
    case NO_PROBATION = 'no probation';
    case ONE_WEEK = '1 week';
    case TWO_WEEKS = '2 weeks';
    case THREE_WEEKS = '3 weeks';
    case ONE_MONTH = '1 month';
    case TWO_MONTHS = '2 months';
    case THREE_MONTHS = '3 months';
    case SIX_MONTHS = '6 months';
    case TWELVE_MONTHS = '12 months';

    public static function options(): array
    {
        return [
            self::NO_PROBATION->value => __('No probation'),
            self::ONE_WEEK->value => __('1 week'),
            self::TWO_WEEKS->value => __('2 weeks'),
            self::THREE_WEEKS->value => __('3 weeks'),
            self::ONE_MONTH->value => __('1 month'),
            self::TWO_MONTHS->value => __('2 months'),
            self::THREE_MONTHS->value => __('3 months'),
            self::SIX_MONTHS->value => __('6 months'),
            self::TWELVE_MONTHS->value => __('12 months'),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::NO_PROBATION => __('No probation'),
            self::ONE_WEEK => __('1 week'),
            self::TWO_WEEKS => __('2 weeks'),
            self::THREE_WEEKS => __('3 weeks'),
            self::ONE_MONTH => __('1 month'),
            self::TWO_MONTHS => __('2 months'),
            self::THREE_MONTHS => __('3 months'),
            self::SIX_MONTHS => __('6 months'),
            self::TWELVE_MONTHS => __('12 months'),
        };
    }

    public static function getProbationOptions(): array
    {
        return collect(self::cases())
            ->map(function (Probation $probation) {
                return [
                    'value' => $probation->value,
                    'label' => $probation->label(),
                ];
            })
            ->toArray();
    }
}
