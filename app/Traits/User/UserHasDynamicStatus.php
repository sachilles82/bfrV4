<?php

namespace App\Traits\User;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\User\UserType;

trait UserHasDynamicStatus
{
    /**
     * Dieses User Model kann verschiede Enum Typen für den Status haben,
     * daher ist der Status dynamisch.
     *
     * User Employee: EmployeeStatus
     * User Partner: PartnerStatus (zukünftig)
     * User Customer: CustomerStatus (zukünftig)
     *
     * Dynamischer Status Accessor - gibt den korrekten Enum-Typ zurück
     * basierend auf dem user_type
     */
    public function getStatusAttribute($value): mixed
    {
        if (!$value) return null;

        return match($this->user_type) {
            UserType::Employee => EmployeeStatus::tryFrom($value),
            // UserType::Partner => PartnerStatus::tryFrom($value),    // Zukünftig
            // UserType::Customer => CustomerStatus::tryFrom($value),  // Zukünftig
            default => null
        };
    }

    /**
     * Dynamischer Status Mutator - konvertiert den Wert zum String für die DB
     * WICHTIG: Dies ist der fehlende Teil!
     */
    public function setStatusAttribute($value): void
    {
        if ($value === null) {
            $this->attributes['status'] = null;
            return;
        }

        // Wenn es bereits ein String ist (z.B. 'probation'), direkt speichern
        if (is_string($value)) {
            $this->attributes['status'] = $value;
            return;
        }

        // Wenn es ein Enum ist, den value extrahieren
        if ($value instanceof \BackedEnum) {
            $this->attributes['status'] = $value->value;
            return;
        }

        // Fallback für andere Fälle
        $this->attributes['status'] = (string) $value;
    }
}
