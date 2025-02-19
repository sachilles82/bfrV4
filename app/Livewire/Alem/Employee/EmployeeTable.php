<?php

namespace App\Livewire\Alem\Employee;

use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use App\Traits\Table\WithSorting;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithPerPagePagination, WithSorting;

    public $selectedIds = [];
    public $idsOnPage = [];
    public $name = '';

    public function delete($userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete();
            $this->dispatch('created');
            session()->flash('delete', __('Employee deleted.'));
        }
    }

    public function deleteUserSelected(): void
    {
        User::whereKey($this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->dispatch('created');
        session()->flash('delete', __('Selected employees deleted.'));
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'sortCol', 'sortAsc');
        $this->dispatch('resetFilters');
    }

    public function edit(int $userId): void
    {
        $this->dispatch('openEmployeeEditModal', $userId);
    }

    protected function applySorting($query)
    {
        if ($this->sortCol) {
            $column = match ($this->sortCol) {
                'name' => 'name',
                'email' => 'email',
                default => 'created_at',
            };
            $query->orderBy($column, $this->sortAsc ? 'asc' : 'desc');
        }
        return $query;
    }

    public function render(): View
    {
        $authUser = auth()->user();

        $query = User::query()
            ->with(['employee', 'teams:id,name', 'roles:id,name']) // Employee-Relation laden
            ->where('company_id', $authUser->company_id)
            ->where('user_type', 'employee'); // Nur Mitarbeiter

        $this->applySearch($query);
        $this->applySorting($query);

        $users = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
        ]);
    }

}
