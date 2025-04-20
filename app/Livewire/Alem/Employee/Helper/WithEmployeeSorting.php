<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Traits\Table\WithSorting;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait für die Sortierung von Mitarbeitern
 * Füge alle Spalten hinzu, nach denen sortiert werden kann
 */
trait WithEmployeeSorting
{
    use WithSorting;

    /**
     * Sortierung auf die Abfrage anwenden
     */
    protected function applySorting(Builder $query): Builder
    {
        if ($this->sortCol) {
            $column = match ($this->sortCol) {
                'name' => 'users.name',
                'joined_at' => 'users.joined_at',
                default => 'users.created_at'
            };
            $query->orderBy($column, $this->sortAsc ? 'asc' : 'desc');
        } else {
            $query->orderBy('users.created_at', 'desc');
        }

        return $query;
    }
}
