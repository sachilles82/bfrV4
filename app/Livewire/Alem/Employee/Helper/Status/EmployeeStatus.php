<?php

namespace App\Livewire\Alem\Employee\Helper\Status;

use App\Traits\Table\WithStatus;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

/**
 * Trait für EmployeeStatus-Integration
 */
trait EmployeeStatus
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
     * NICHT MEHR BENÖTIGT - arbeiten direkt mit users Tabelle
     */
    protected function getRelationName(): string
    {
        return ''; // Keine Relation mehr nötig
    }

    /**
     * Gibt den Namen des Status-Felds in der Relation zurück
     */
    protected function getStatusFieldName(): string
    {
        return 'status';
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
        if (!empty($this->employeeStatusFilter)) {
            $query->where('users.status', $this->employeeStatusFilter);
        }

        return $query;
    }

    /**
     * Nur den Employee-Status-Filter zurücksetzen
     */
    public function resetEmployeeStatusFilter(): void
    {
        $this->resetModelSpecificStatusFilter();
    }

}
