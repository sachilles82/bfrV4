<?php

namespace App\Livewire\Account\Employee;

use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use App\Traits\Table\WithSorting;
use App\Livewire\HR\Department\Helper\Searchable; // Universeller Searchable Trait
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Attributes\On;

class EmployeeTable extends Component
{
    use Searchable, WithPerPagePagination, WithSorting;

    public $selectedIds = [];
    public $idsOnPage = [];

    // Optionale Filter oder weitere Properties
    public $name = '';

    /**
     * Löscht einen Mitarbeiter (User).
     */
    public function delete($userId): void
    {
        $user = User::find($userId);
        // Hier ggf. Autorisierung prüfen...
        if ($user) {
            $user->delete();
            $this->dispatch('created');
            session()->flash('delete', __('Employee deleted.'));
        }
    }

    /**
     * Löscht ausgewählte Mitarbeiter.
     */
    public function deleteUserSelected(): void
    {
        User::whereKey($this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->dispatch('created');
        session()->flash('delete', __('Selected employees deleted.'));
    }

    /**
     * Setzt Filter zurück.
     */
    public function resetFilters(): void
    {
        $this->reset('search', 'sortCol', 'sortAsc');
        $this->dispatch('resetFilters');
    }

    /**
     * Öffnet das Edit-Modal für einen Mitarbeiter.
     *
     * Hier kannst du entweder direkt in dieser Komponente
     * ein Edit-Formular anzeigen oder per Event an eine
     * separate Livewire-Komponente übergeben.
     */
    public function edit(int $userId): void
    {
        // Hier dispatchten wir ein Event, das z. B. von einem separaten Edit-Component (EmployeeUpdate)
        // abgefangen wird. Alternativ kannst du auch in dieser Komponente das Edit-Modal einbetten.
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

        // Such- und Sortierfunktionen anwenden
        $this->applySearch($query);
        $this->applySorting($query);

        $users = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.account.employee.employee-table', [
            'users' => $users,
        ]);
    }
}
