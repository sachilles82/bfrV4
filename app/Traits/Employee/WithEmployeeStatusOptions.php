<?php

namespace App\Traits\Employee;

use App\Enums\Employee\EmployeeStatus;

/**
 * Trait für die Bereitstellung von Employee-Status-Optionen
 * @package App\Traits\Employee
 */
trait WithEmployeeStatusOptions
{
    /**
     * Gibt alle verfügbaren Mitarbeiterstatus-Optionen mit ihren Labels, Farben und Icons zurück
     *
     * Diese Computed Property bereitet die Daten für die Anzeige in Select-Komponenten vor
     * und sorgt für eine einheitliche Darstellung in der gesamten Anwendung.
     *
     * @return array Array mit Optionen für Dropdown-Menüs und andere UI-Elemente
     */
    public function getEmployeeStatusOptionsProperty()
    {
        $statuses = [];

        foreach (EmployeeStatus::cases() as $status) {
            $statuses[] = [
                'value' => $status->value,
                'label' => $status->label(),
                'colors' => $status->colors(),
                'icon' => $status->icon(),
            ];
        }

        return $statuses;
    }
}
