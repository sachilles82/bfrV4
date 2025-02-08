<?php

namespace App\Enums\Company;

enum CompanySize: string
{
    case OneToFive = '1-5';
    case SixToTen = '6-10';
    case ElevenToTwenty = '11-20';
    case TwentyOneToFifty = '21-50';
    case FiftyOneToOneHundred = '51-100';
    case OneHundredOneToTwoHundred = '101-200';
    case MoreThanTwoHundred = '>200';

    public static function options(): array
    {
        return [
            self::OneToFive->value => '1-5',
            self::SixToTen->value => '6-10',
            self::ElevenToTwenty->value => '11-20',
            self::TwentyOneToFifty->value => '21-50',
            self::FiftyOneToOneHundred->value => '51-100',
            self::OneHundredOneToTwoHundred->value => '101-200',
            self::MoreThanTwoHundred->value => '>200',
        ];
    }

}
