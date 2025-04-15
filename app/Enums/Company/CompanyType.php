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
            self::Einzelfirma => __('Einzelfirma'),
            self::GmbH => __('GmbH'),
            self::AG => __('AG'),
        };
    }
}
