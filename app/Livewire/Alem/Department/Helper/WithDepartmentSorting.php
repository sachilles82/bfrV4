<?php

namespace App\Livewire\Alem\Department\Helper;

use App\Traits\Table\WithSorting;
use Illuminate\Database\Eloquent\Builder;

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
                'joined_at' => 'joined_at',
                'created_at' => 'created_at',
            };
            $query->orderBy($column, $this->sortAsc ? 'asc' : 'desc');
        }
        return $query;
    }
}
