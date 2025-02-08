<?php

namespace App\Enums\Company;

enum CompanyType: string
{
    case Einzelfirma = 'einzelfirma';
    case GmbH = 'gmbh';
    case AG = 'ag';


    public static function options(): array
    {
        return [
            self::Einzelfirma->value => 'Einzelfirma',
            self::GmbH->value => 'GmbH',
            self::AG->value => 'AG',
        ];
    }

    public function label(): string
    {
        return match ($this) {
            static::Einzelfirma => __('Einzelfirma'),
            static::GmbH => __('GmbH'),
            static::AG => __('AG'),
        };
    }
}
