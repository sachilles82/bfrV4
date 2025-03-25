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
            self::NO_PROBATION->value => 'No probation',
            self::ONE_WEEK->value => '1 week',
            self::TWO_WEEKS->value => '2 weeks',
            self::THREE_WEEKS->value => '3 weeks',
            self::ONE_MONTH->value => '1 month',
            self::TWO_MONTHS->value => '2 months',
            self::THREE_MONTHS->value => '3 months',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            static::NO_PROBATION => __('No probation'),
            static::ONE_WEEK => __('1 week'),
            static::TWO_WEEKS => __('2 weeks'),
            static::THREE_WEEKS => __('3 weeks'),
            static::ONE_MONTH => __('1 month'),
            static::TWO_MONTHS => __('2 months'),
            static::THREE_MONTHS => __('3 months'),
        };
    }
}
