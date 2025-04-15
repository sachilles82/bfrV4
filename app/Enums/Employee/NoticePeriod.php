<?php

namespace App\Enums\Employee;

enum NoticePeriod: string
{
    case NO_NOTICE = 'no notice period';
    case ONE_WEEK = '1 week';
    case TWO_WEEKS = '2 weeks';
    case THREE_WEEKS = '3 weeks';
    case ONE_MONTH = '1 month';
    case TWO_MONTHS = '2 months';
    case THREE_MONTHS = '3 months';
    case FOUR_MONTHS = '4 months';
    case FIVE_MONTHS = '5 months';
    case SIX_MONTHS = '6 months';
    case SEVEN_MONTHS = '7 months';
    case EIGHT_MONTHS = '8 months';
    case NINE_MONTHS = '9 months';
    case TEN_MONTHS = '10 months';
    case ELEVEN_MONTHS = '11 months';
    case TWELVE_MONTHS = '12 months';

    public static function options(): array
    {
        return [
            self::NO_NOTICE->value => 'No notice period',
            self::ONE_WEEK->value => '1 week',
            self::TWO_WEEKS->value => '2 weeks',
            self::THREE_WEEKS->value => '3 weeks',
            self::ONE_MONTH->value => '1 month',
            self::TWO_MONTHS->value => '2 months',
            self::THREE_MONTHS->value => '3 months',
            self::FOUR_MONTHS->value => '4 months',
            self::FIVE_MONTHS->value => '5 months',
            self::SIX_MONTHS->value => '6 months',
            self::SEVEN_MONTHS->value => '7 months',
            self::EIGHT_MONTHS->value => '8 months',
            self::NINE_MONTHS->value => '9 months',
            self::TEN_MONTHS->value => '10 months',
            self::ELEVEN_MONTHS->value => '11 months',
            self::TWELVE_MONTHS->value => '12 months',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::NO_NOTICE => __('No notice period'),
            self::ONE_WEEK => __('1 week'),
            self::TWO_WEEKS => __('2 weeks'),
            self::THREE_WEEKS => __('3 weeks'),
            self::ONE_MONTH => __('1 month'),
            self::TWO_MONTHS => __('2 months'),
            self::THREE_MONTHS => __('3 months'),
            self::FOUR_MONTHS => __('4 months'),
            self::FIVE_MONTHS => __('5 months'),
            self::SIX_MONTHS => __('6 months'),
            self::SEVEN_MONTHS => __('7 months'),
            self::EIGHT_MONTHS => __('8 months'),
            self::NINE_MONTHS => __('9 months'),
            self::TEN_MONTHS => __('10 months'),
            self::ELEVEN_MONTHS => __('11 months'),
            self::TWELVE_MONTHS => __('12 months'),
        };
    }
}
