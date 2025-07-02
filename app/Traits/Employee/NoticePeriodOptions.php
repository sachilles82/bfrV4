<?php

namespace App\Traits\Employee;

use App\Enums\Employee\NoticePeriod;
use Livewire\Attributes\Computed;

trait NoticePeriodOptions
{
    /**
     * Gibt die Optionen für die Kündigungsfrist zurück
     * @return array Array mit Optionen für Dropdown-Menüs und andere UI-Elemente
     */
    #[Computed]
    public function noticePeriodOptions(): array
    {
        return NoticePeriod::getNoticePeriodOptions();
    }
}
