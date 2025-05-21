<?php

namespace App\Traits\Employee;

use App\Enums\Employee\EmployeeStatus;
use Livewire\Attributes\Computed;

trait EmployeeStatusOptions
{
    /**
     * Gibt die Optionen für den Mitarbeiterstatus zurück
     * @return array Array mit Optionen für Dropdown-Menüs und andere UI-Elemente
     */
    #[Computed]
    public function employeeStatusOptions(): array
    {
        return EmployeeStatus::getEmployeeOptions();
    }
}
