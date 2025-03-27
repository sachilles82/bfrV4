<?php

namespace App\Livewire\Alem\Department\Helper;

use App\Models\Alem\Department;
use App\Traits\Model\ModelStatusAction;

trait WithDepartmentModelStatus
{
    use ModelStatusAction;


    /**
     * Die Modellklasse für ModelStatusAction
     */
    protected function getModelClass(): string
    {
        return Department::class;
    }

    /**
     * Der Anzeigename für das Modell
     */
    protected function getModelDisplayName(): string
    {
        return 'Department';
    }

    /**
     * Der pluralisierte Anzeigename für das Modell
     */
    protected function getModelDisplayNamePlural(): string
    {
        return 'Departments';
    }

    /**
     * Der Benutzertyp für die Filterung
     */
    protected string $DepartmentType = 'department';

    /**
     * Name des Events, das nach Status-Änderungen ausgelöst wird
     */
    protected function getStatusUpdateEventName(): string
    {
        return 'departmentUpdated';
    }

    /**
     * Sendet ein modellspezifisches Event
     * 
     * @param string $action Die Aktion (z.B. 'created', 'updated', 'deleted')
     */
    protected function dispatchModelEvent(string $action): void
    {
        // Hier wird direkt der Modeltyp (z.B. 'department') + Aktion gesendet
        $modelType = strtolower($this->getModelDisplayName());
        $this->dispatch("{$modelType}-{$action}");
    }

    /**
     * Sendet ein Event zur Aktualisierung der modellspezifischen Tabelle
     * Ersetzt den generischen 'update-table' Event
     */
    protected function dispatchTableUpdateEvent(): void
    {
        $modelType = strtolower($this->getModelDisplayName());
        $this->dispatch("{$modelType}-table-updated");
    }
}
