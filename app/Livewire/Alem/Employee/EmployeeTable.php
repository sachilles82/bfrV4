<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\User\AccountStatus;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Livewire\Alem\Traits\UserStatusAction;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use App\Traits\Table\WithSorting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithPerPagePagination, WithSorting, UserStatusAction;

    public $selectedIds = [];
    public $idsOnPage = [];
    public $name = '';
    public $statusFilter = 'active';

    /**
     * Der Benutzertyp für die Filterung
     */
    protected string $userType = 'employee';

    /**
     * Name des Events, das nach Status-Änderungen ausgelöst wird
     */
    protected function getStatusUpdateEventName(): string
    {
        return 'employeeUpdated';
    }

    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich eine Property ändert
     */
    public function updated($property): void
    {
        // Wenn sich der Status-Filter ändert, die Auswahl zurücksetzen
        if ($property === 'statusFilter') {
            $this->resetSelections();
            $this->dispatch('update-table');
        }
    }

    /**
     * Filter zurücksetzen
     */
    public function resetFilters(): void
    {
        $this->reset('search', 'sortCol', 'sortAsc', 'statusFilter');
        $this->resetSelections();
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    /**
     * Sortierung auf die Abfrage anwenden
     */
    protected function applySorting(Builder $query): Builder
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
            ->with(['employee', 'teams:id,name', 'roles:id,name'])
            ->where('company_id', $authUser->company_id)
            ->where('user_type', $this->userType);

        $this->applySearch($query);
        $this->applySorting($query);
        $this->applyStatusFilter($query);

        $users = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => AccountStatus::cases(),
        ]);
    }
}
