<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\User\AccountStatus;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Livewire\Alem\Employee\Helper\UserStatusAction;
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

    // Status-Filter auf die Abfrage anwenden
    protected function applyStatusFilter(Builder $query): Builder
    {
        switch ($this->statusFilter) {
            case 'inactive':
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::INACTIVE->value);
                break;

            case 'archived':
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ARCHIVED->value);
                break;

            case 'trashed':
                $query->onlyTrashed();
                break;

            case 'active':
            default:
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ACTIVE->value);
        }

        return $query;
    }

    // Sortierung auf die Abfrage anwenden
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

    /**
     * Render-Methode
     */
    public function render(): View
    {
        $authUser = auth()->user();

        $query = User::query()
            ->with(['employee', 'teams:id,name', 'roles:id,name'])
            ->where('company_id', $authUser->company_id)
            ->where('user_type', 'employee');

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
