<?php

namespace App\Livewire\Alem\Department\Helper;

use App\Traits\Table\WithSorting;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait für die Sortierung von Abteilungen
 * Füge alle Spalten hinzu, nach denen sortiert werden kann
 */
trait WithDepartmentSorting
{
    use WithSorting;

    /**
     * Sortierung auf die Abfrage anwenden
     */
    protected function applySorting(Builder $query): Builder
    {
        if ($this->sortCol) {
            $column = match ($this->sortCol) {
                'name' => 'name',
                default => 'created_at',
            };
            $query->orderBy($column, $this->sortAsc ? 'asc' : 'desc');
        }

        return $query;
    }
}
