<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleVisibility;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Livewire\Alem\Employee\Helper\WithEmployeeModelStatus;
use App\Livewire\Alem\Employee\Helper\WithEmployeeSorting;
use App\Livewire\Alem\Employee\Helper\WithEmployeeStatus;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithPerPagePagination, WithEmployeeSorting,
        WithEmployeeModelStatus,
        WithEmployeeStatus;

    public $name = '';

    public $teamFilter = null;

    /** Tabelle zeigt nur User mit user_typ employee */
    protected string $userType = 'employee';

    /**
     * Hört auf das Event 'employee-created', 'employee-updated' und aktualisiert die Tabelle
     */
    #[On(['employee-created', 'employee-updated'])]
    public function refreshTable(): void
    {
        $this->resetPage();
        Cache::forget($this->getCacheKey('teams'));
    }

    /** Lifecycle-Hook: Wird aufgerufen, wenn sich ein Filter ändert, die Auswahl zurücksetzen */
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
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    protected function getCacheKey(string $suffix): string
    {
        $userId = auth()->id();
        $companyId = auth()->user()->company_id;
        return "employee-table.{$userId}.{$companyId}.{$suffix}";
    }

    /**
     * Get available teams for filtering
     */
    public function getAvailableTeamsProperty()
    {
        return Cache::remember($this->getCacheKey('teams'), now()->addMinutes(30), function() {
            return auth()->user()->allTeams();
        });
    }

    /**
     * Öffnet den Bearbeitungsmodus für einen Mitarbeiter
     */
    public function edit($id): void
    {
        $this->dispatch('edit-employee', $id);
    }

    /**
     * Wendet den Team-Filter auf die Abfrage an
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyTeamFilter($query)
    {
        if (!empty($this->teamFilter)) {
            return $query->whereHas('teams', function ($teamQuery) {
                $teamQuery->where('teams.id', $this->teamFilter);
            });
        }

        return $query;
    }

    protected function activeEmployees($query)
    {
        return $query->where('model_status', ModelStatus::ACTIVE)
            ->where('user_type', UserType::Employee);
    }
    /**
     * Rendert die Mitarbeitertabelle mit allen notwendigen Daten
     * Optimiert für exakte Feldauswahl und Performance
     *
     * @return \Illuminate\Contracts\View\View
     */
    // In app/Livewire/Alem/Employee/EmployeeTable.php

    public function render(): View
    {
        // Verbesserte Query mit gezielter Feldauswahl und optimierten Joins
        $query = User::query()
            ->select([
                'id', 'user_type', 'name', 'last_name', 'phone_1',
                'email', 'joined_at', 'created_at', 'model_status',
                'company_id', 'department_id', 'profile_photo_path', 'slug'
            ])
            ->where('user_type', $this->userType)
            ->where('model_status', ModelStatus::ACTIVE)
            ->whereHas('employee');

        // Effizientes Laden der Beziehungen mit expliziten Feldangaben
        $query->with([
            'employee' => function($query) {
                $query->select(['id', 'user_id', 'employee_status', 'profession_id', 'stage_id']);
            },
            'employee.profession:id,name',
            'employee.stage:id,name',
            'department:id,name',
            'teams:id,name',
            'roles' => function($query) {
                $query->where('visible', RoleVisibility::Visible->value)
                    ->select('id', 'name', 'is_manager');
            }
        ]);

        // Anwenden der Filter
        $this->applySearch($query);
        $this->applySorting($query);
        $this->applyStatusFilter($query);

        if ($this->employeeStatusFilter) {
            $query->whereHas('employee', function($q) {
                $q->where('employee_status', $this->employeeStatusFilter);
            });
        }

        if ($this->teamFilter) {
            $query->whereHas('teams', function($q) {
                $q->where('teams.id', $this->teamFilter);
            });
        }

        // Paginierung anwenden
        $users = $query->paginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => ModelStatus::cases(),
            'employeeStatuses' => EmployeeStatus::cases(),
            'availableTeams' => $this->availableTeams,
        ]);
    }
}
