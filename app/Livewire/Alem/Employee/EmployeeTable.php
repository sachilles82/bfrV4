<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Livewire\Alem\Employee\Helper\WithEmployeeModelStatus;
use App\Livewire\Alem\Employee\Helper\WithEmployeeSorting;
use App\Livewire\Alem\Employee\Helper\WithEmployeeStatus;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithEmployeeModelStatus, WithEmployeeSorting,
        WithEmployeeStatus,
        WithPerPagePagination;

    // Eigenschaften für vorgeladene Daten
    public int $authUserId;
    public int $currentTeamId;
    public int $companyId;

    /**
     * Tabelle zeigt nur User mit user_typ employee
     */
    protected string $userType = UserType::Employee->value;


    public function mount(int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;
    }

    /**
     * Hört auf das Event 'employee-created', 'employee-updated' und aktualisiert die Tabelle
     */
    #[On(['employee-created', 'employee-updated'])]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich eine öffentliche Eigenschaft ändert.
     * Setzt die Auswahl zurück, wenn Filter geändert werden.
     */
    public function updated($property): void
    {

        if (in_array($property, ['statusFilter', 'employeeStatusFilter'])) {
            $this->selectedIds = [];
            $this->reset('search', 'sortCol', 'sortAsc', 'perPage');
            $this->dispatch('update-table');
        }
    }

    /**
     * Alle Filter zurücksetzen
     */
    public function resetFilters(): void
    {
        $this->resetPage();
        $this->reset('search', 'sortCol', 'sortAsc', 'statusFilter', 'employeeStatusFilter');
        $this->selectedIds = [];
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $authCurrentTeamId = $this->currentTeamId;
        $companyId = $this->companyId;

        $query = User::query();

        // ----------- FORCE INDEX nur wenn nicht gesucht wird -----------
        if (empty($this->search)) {
            // Index nur erzwingen, wenn KEINE Suche stattfindet
            $query->from(DB::raw('`users` FORCE INDEX (`idx_users_filter_sort`)'));
        } else {
            // Wenn gesucht wird, NICHT den Index erzwingen,
            // damit der FULLTEXT Index genutzt werden kann.
            $query->from('users'); // Explizit setzen schadet nicht
        }
        // ----------------------- Ende FORCE INDEX --------------------------------

        $query->select([
            'users.id', 'users.department_id', 'users.name', 'users.last_name', 'users.phone_1',
            'users.email', 'users.joined_at', 'users.created_at', 'users.model_status',
            'users.profile_photo_path', 'users.slug', 'users.deleted_at',
            'employees.id as employee_id', 'employees.employee_status', 'employees.profession_id',
            'employees.stage_id', 'professions.name as profession_name',
            'stages.name as stage_name', 'departments.name as department_name'
        ]);

        $query->with([
            'roles' => function ($q_roles) use ($companyId) {
                $q_roles->where('visible', RoleVisibility::Visible->value)
                    ->where('access', RoleHasAccessTo::EmployeePanel->value)
                    ->where(function($query) use ($companyId) {
                        $query->where('created_by', 1)
                            ->orWhere('company_id', $companyId);
                    })
                    ->select('roles.id', 'roles.name');
            },
        ]);

        // Joins hinzufügen
        $query->join('employees', 'users.id', '=', 'employees.user_id')
            ->join('team_user', function ($join) use ($authCurrentTeamId) {
                $join->on('users.id', '=', 'team_user.user_id')
                    ->where('team_user.team_id', '=', $authCurrentTeamId);
            })
            ->leftJoin('professions', 'employees.profession_id', '=', 'professions.id')
            ->leftJoin('stages', 'employees.stage_id', '=', 'stages.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id');

        // WHERE Bedingungen für den Basiszustand (ohne Suche/Filter)
        $query->where('users.user_type', $this->userType);

        $this->applySearch($query);
        $this->applyStatusFilter($query);
        if ($this->employeeStatusFilter) {
            $query->where('employees.employee_status', $this->employeeStatusFilter);
        }


        $this->applySorting($query);

        $users = $query->simplePaginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => ModelStatus::cases(),
            'employeeStatuses' => EmployeeStatus::cases(),
        ]);
    }

    public function placeholder():View
    {
        return view('livewire.placeholders.employee.index');
    }
}
