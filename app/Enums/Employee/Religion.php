<?php

namespace App\Enums\Employee;

enum Religion: string
{
    case Catholic = 'catholic';
    case ChristianCatholic = 'christian-catholic';
    case Reformed = 'evangelical-reformed';
    case Muslim = 'muslim';
    case Other = 'other';
    case NoConfession = 'noconfession';

    public static function options(): array
    {
        return [
            self::Catholic->value => 'Roman Catholic',
            self::ChristianCatholic->value => 'Christian Catholic',
            self::Reformed->value => 'Reformed',
            self::Muslim->value => 'Muslim',
            self::Other->value => 'Other',
            self::NoConfession->value => 'No Confession',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Catholic => 'Roman Catholic',
            self::ChristianCatholic => 'Christian Catholic',
            self::Reformed => 'Reformed',
            self::Muslim => 'Muslim',
            self::Other => 'Other',
            self::NoConfession => 'No Confession',
        };
    }
}
