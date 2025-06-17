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
            self::Catholic->value => __('Roman Catholic'),
            self::ChristianCatholic->value => __('Christian Catholic'),
            self::Reformed->value => __('Reformed'),
            self::Muslim->value => __('Muslim'),
            self::Other->value => __('Other'),
            self::NoConfession->value => __('No Confession'),
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Catholic => __('Roman Catholic'),
            self::ChristianCatholic => __('Christian Catholic'),
            self::Reformed => __('Reformed'),
            self::Muslim => __('Muslim'),
            self::Other => __('Other'),
            self::NoConfession => __('No Confession'),
        };
    }

    public static function getReligionOptions(): array
    {
        return collect(self::cases())
            ->map(function (Religion $religion) {
                return [
                    'value' => $religion->value,
                    'label' => $religion->label(),
                ];
            })
            ->toArray();
    }
}
