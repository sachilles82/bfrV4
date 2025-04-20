<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Models\User;
use App\Traits\Model\ModelStatusAction;

/**
 * Trait für ModelStatus-Integration
 */
trait WithEmployeeModelStatus
{
    use ModelStatusAction;

    /**
     * Die Modellklasse für ModelStatusAction
     */
    protected function getModelClass(): string
    {
        return User::class;
    }

    /**
     * Spezifizieren, dass die Filterung des Mitarbeiterstatus die users-Tabelle verwenden soll
     */
    protected function getStatusFilterTable(): string
    {
        return 'users';
    }

    /**
     * Der Anzeigename für das Modell
     */
    protected function getModelDisplayName(): string
    {
        return 'Employee';
    }

    /**
     * Der pluralisierte Anzeigename für das Modell
     */
    protected function getModelDisplayNamePlural(): string
    {
        return 'Employees';
    }
    //
    //    /**
    //     * Name des Events, das nach Status-Änderungen ausgelöst wird
    //     */
        protected function getStatusUpdateEventName(): string
        {
            return 'employeeUpdated';
        }
    //
    //    /**
    //     * Sendet ein modellspezifisches Event
    //     *
    //     * @param string $action Die Aktion (z.B. 'created', 'updated', 'deleted')
    //     */
    //    protected function dispatchModelEvent(string $action): void
    //    {
    //        // Hier wird direkt der Modeltyp (z.B. 'employee') + Aktion gesendet
    //        $modelType = strtolower($this->getModelDisplayName());
    //        $this->dispatch("{$modelType}-{$action}");
    //    }
    //
    //    /**
    //     * Sendet ein Event zur Aktualisierung der modellspezifischen Tabelle
    //     * Ersetzt den generischen 'update-table' Event
    //     */
    //    protected function dispatchTableUpdateEvent(): void
    //    {
    //        $modelType = strtolower($this->getModelDisplayName());
    //        $this->dispatch("{$modelType}-table-updated");
    //    }
}
