<?php

namespace App\Traits\Table;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

/**
 * Basis-Trait für modellspezifische Status-Filter in einzelnen Models. Wie Employee, Report, etc.
 * Wird von konkreten Status-Traits wie EmployeeStatus, WithReportStatus usw. verwendet
 */
trait WithStatus
{
    #[Url]
    public string $modelStatusFilter = '';

    /**
     * Gibt den Namen des Status-Filters zurück
     * Muss in abgeleiteten Traits überschrieben werden
     */
    abstract protected function getStatusFilterName(): string;

    /**
     * Gibt den Namen der Relation zurück
     * Muss in abgeleiteten Traits überschrieben werden
     */
    abstract protected function getRelationName(): string;

    /**
     * Gibt den Namen des Status-Felds in der Relation zurück
     * Muss in abgeleiteten Traits überschrieben werden
     */
    abstract protected function getStatusFieldName(): string;

    /**
     * Gibt den Namen der Reset-Methode zurück
     * Kann in abgeleiteten Traits überschrieben werden
     */
    protected function getResetMethodName(): string
    {
        return 'reset'.ucfirst($this->getStatusFilterName()).'Filter';
    }

    /**
     * Gibt den Namen der Apply-Methode zurück
     * Kann in abgeleiteten Traits überschrieben werden
     */
    protected function getApplyMethodName(): string
    {
        return 'apply'.ucfirst($this->getStatusFilterName()).'Filter';
    }

    /**
     * Initialisiert das Trait
     * Wird automatisch von Livewire aufgerufen
     */
    public function bootModelSpecificStatus(): void
    {
        // Dynamisch die Property für den Filter erstellen
        $filterName = $this->getStatusFilterName().'Filter';
        $this->$filterName = $this->modelStatusFilter;
    }

    /**
     * Status-Filter auf die Abfrage anwenden
     * Generische Implementierung, die für die meisten Fälle funktioniert
     */
    protected function applyModelSpecificStatusFilter(Builder $query): Builder
    {
        $filterName = $this->getStatusFilterName().'Filter';

        if (! empty($this->$filterName)) {
            $query->whereHas($this->getRelationName(), function ($query) use ($filterName) {
                $query->where($this->getStatusFieldName(), $this->$filterName);
            });
        }

        return $query;
    }

    /**
     * Status-Filter zurücksetzen
     */
    protected function resetModelSpecificStatusFilter(): void
    {
        $filterName = $this->getStatusFilterName().'Filter';
        $this->$filterName = '';

        if (property_exists($this, 'selectedIds')) {
            $this->selectedIds = [];
        }

        $this->dispatch('update-table');
    }

    /**
     * Alle Filter zurücksetzen
     */
    public function setAllModelSpecificStatus(): void
    {
        $filterName = $this->getStatusFilterName().'Filter';
        $this->$filterName = '';

        if (method_exists($this, 'reset')) {
            $this->reset('search', 'sortCol', 'sortAsc');
        }

        if (property_exists($this, 'selectedIds')) {
            $this->selectedIds = [];
        }

        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }
}
