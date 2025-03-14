<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Livewire\Alem\Employee\Helper\WithEmployeeSorting;
use App\Livewire\Alem\Employee\Helper\WithEmployeeStatus;
use App\Livewire\Alem\Employee\Helper\WithModelStatus;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithPerPagePagination, WithEmployeeSorting, WithModelStatus, WithEmployeeStatus;

    public $selectedIds = [];
    public $idsOnPage = [];
    public $name = '';


    /**
     * Filterung nach User-Typ aus der User-Tabelle. Nur für User nötig
     */
    protected string $userType = 'employee';


    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich ein Filter ändert, die Auswahl zurücksetzen
     */
    public function updated($property): void
    {
        if (in_array($property, ['statusFilter', 'employeeStatusFilter'])) {
            $this->selectedIds = [];
            $this->reset('search', 'sortCol', 'sortAsc');

            if ($property === 'statusFilter') {
                $this->reset('employeeStatusFilter');
            }

            $this->dispatch('update-table');
        }
    }

    /**
     * Alle Filter zurücksetzen
     */
    public function resetFilters(): void
    {
        $this->resetPage();
        $this->reset('search');
        $this->reset('sortCol', 'sortAsc', 'statusFilter', 'employeeStatusFilter');
        $this->selectedIds = [];
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    public function render(): View
    {
        $authUser = auth()->user();

        $query = User::query()
            ->with(['employee', 'teams:id,name', 'roles:id,name'])
            ->whereHas('employee')
            ->where('company_id', $authUser->company_id)
            ->where('user_type', $this->userType);

        $this->applySearch($query);
        $this->applySorting($query);

        // Filter nach ModelStatus, falls gesetzt
        $this->applyStatusFilter($query);

        // Filter nach EmployeeStatus, falls gesetzt
        $this->applyEmployeeStatusFilter($query);

        $users = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => ModelStatus::cases(),
            'employeeStatuses' => EmployeeStatus::cases(),
        ]);
    }
}
