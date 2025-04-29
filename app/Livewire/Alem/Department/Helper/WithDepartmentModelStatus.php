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

}
