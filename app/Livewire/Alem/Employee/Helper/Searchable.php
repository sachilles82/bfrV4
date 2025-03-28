<?php

namespace App\Livewire\Alem\Employee\Helper;

trait Searchable
{
    public $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    protected function applySearch($query)
    {
        return $this->search === ''
            ? $query
            : $query->where('name', 'like', "%{$this->search}%")
            ->orWhere('last_name', 'like', "%{$this->search}%")
                ->orWhere('phone_1', 'like', "%{$this->search}%");
    }
}
