<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleVisibility;
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

    public $teamFilter = null;

    /**
     * Apply team filter to the query
     */
    protected function applyTeamFilter($query)
    {
        if ($this->teamFilter) {
            $query->whereHas('teams', function ($q) {
                $q->where('teams.id', $this->teamFilter);
            });
        }
    }


    /**
     * Filterung nach User-Typ aus der User-Tabelle. Nur für User nötig
     */
    protected string $userType = 'employee';


    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich ein Filter ändert, die Auswahl zurücksetzen
     */
    public function updated($property): void
    {
        if (in_array($property, ['statusFilter', 'employeeStatusFilter', 'teamFilter'])) {
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
        $this->reset('sortCol', 'sortAsc', 'statusFilter', 'employeeStatusFilter', 'teamFilter');
        $this->selectedIds = [];
        $this->teamFilter = null;
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    /**
     * Get available teams for filtering
     */
    public function getAvailableTeamsProperty()
    {
        return auth()->user()->allTeams();
    }

    public function render(): View
    {
        $authUser = auth()->user();

//        $query = User::query()
//            ->with([
//                'employee',
//                'teams:id,name',
//                'roles' => function($query) {
//                    $query->where('visible', RoleVisibility::Visible->value)->select('id', 'name');
//                }
//            ])
//            ->whereHas('employee')
//            ->where('company_id', $authUser->company_id)
//            ->where('user_type', $this->userType);

        $query = User::query()
            ->with([
                'employee',
                'teams:id,name',
                'currentTeam:id,name', // Add this to load the current team explicitly
                'roles' => function($query) {
                    $query->where('visible', RoleVisibility::Visible->value)->select('id', 'name');
                }
            ])
            ->whereHas('employee')
            ->where('company_id', $authUser->company_id)
            ->where('user_type', $this->userType);

        $this->applySearch($query);
        $this->applySorting($query);
        $this->applyStatusFilter($query);
        $this->applyEmployeeStatusFilter($query);
        $this->applyTeamFilter($query); // Apply team filter

        $users = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => ModelStatus::cases(),
            'employeeStatuses' => EmployeeStatus::cases(),
            'availableTeams' => $this->availableTeams,
        ]);
    }
}
