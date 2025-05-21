<?php

namespace App\Enums\User;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Other = 'other';

    public static function options(): array
    {
        return [
            self::Male->value => 'Male',
            self::Female->value => 'Female',
            self::Other->value => 'Other',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Male => __('Male'),
            self::Female => __('Female'),
            self::Other => __('Other'),
        };
    }

    public static function getGenderOptions(): array
    {
        return collect(Gender::cases())
            ->map(function (Gender $gender) {
                return [
                    'value' => $gender->value,
                    'label' => $gender->label(),
                ];
            })
            ->toArray();
    }
}
