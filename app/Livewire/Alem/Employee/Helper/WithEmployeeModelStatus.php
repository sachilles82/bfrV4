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

}
