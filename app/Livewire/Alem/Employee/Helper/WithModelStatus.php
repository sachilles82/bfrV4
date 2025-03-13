<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Models\User;
use App\Traits\Model\ModelStatusAction;
use Livewire\Attributes\Url;

/**
 * Trait für ModelStatus-Integration
 */

trait WithModelStatus
{
    use ModelStatusAction;

    #[Url]
    public $statusFilter = 'active';

    /**
     * Die Modellklasse für ModelStatusAction
     */
    protected function getModelClass(): string
    {
        return User::class;
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

    /**
     * Name des Events, das nach Status-Änderungen ausgelöst wird
     */
    protected function getStatusUpdateEventName(): string
    {
        return 'employeeUpdated';
    }

}
