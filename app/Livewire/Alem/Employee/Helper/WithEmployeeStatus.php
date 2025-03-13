<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Traits\Table\WithStatus;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

trait WithEmployeeStatus
{
    use WithStatus;

    #[Url]
    public $employeeStatusFilter = '';

    /**
     * Gibt den Namen des Status-Filters zurück
     */
    protected function getStatusFilterName(): string
    {
        return 'employeeStatus';
    }

    /**
     * Gibt den Namen der Relation zurück
     */
    protected function getRelationName(): string
    {
        return 'employee';
    }

    /**
     * Gibt den Namen des Status-Felds in der Relation zurück
     */
    protected function getStatusFieldName(): string
    {
        return 'employee_status';
    }

    /**
     * Setzt den Mitarbeiterstatus-Filter zurück und bereinigt andere Suchkriterien
     */
    public function setAllStatus(): void
    {
        $this->setAllModelSpecificStatus();
    }

    /**
     * Employee-Status-Filter auf die Abfrage anwenden
     */
    protected function applyEmployeeStatusFilter(Builder $query): Builder
    {
        return $this->applyModelSpecificStatusFilter($query);
    }

    /**
     * Nur den Employee-Status-Filter zurücksetzen
     */
    public function resetEmployeeStatusFilter(): void
    {
        $this->resetModelSpecificStatusFilter();
    }
}
