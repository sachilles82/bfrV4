<?php

namespace App\Traits\Employee;

use App\Enums\Employee\Probation;
use Livewire\Attributes\Computed;

trait ProbationOptions
{
    /**
     * Gibt die Optionen für die Probezeit zurück
     * @return array Array mit Optionen für Dropdown-Menüs und andere UI-Elemente
     */
    #[Computed]
    public function probationOptions(): array
    {
        return Probation::getProbationOptions();
    }
}
