<?php

namespace App\Livewire\HR\Department\Helper;

trait Searchable
{
    public $search = '';

    public function updatedSearchable($property): void
    {
        if ($property === 'search'){
            $this->resetPage();
        }
    }

    protected function applySearch($query)
    {
        return $this->search === ''
            ? $query
            : $query->where('name', 'like', "%{$this->search}%");
    }
}
