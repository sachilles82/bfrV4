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
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy(isolate: false)]
class EmployeeTable extends Component
{
    use Searchable, WithEmployeeModelStatus, WithEmployeeSorting,
        WithEmployeeStatus,
        WithPerPagePagination;

    /**
     * Tabelle zeigt nur User mit user_typ employee
     */
    protected string $userType = UserType::Employee->value;

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
        // Prüfen, ob sich einer der Filter geändert hat
        if (in_array($property, [
            'search',
            'statusFilter',
            'employeeStatusFilter',
            'sortCol',
            'sortAsc',
            'perPage',
        ])) {
            $this->selectedIds = [];

            if ($property !== 'perPage') {
                $this->resetPage();
            }

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
        $this->reset('search', 'sortCol', 'sortAsc', 'statusFilter', 'employeeStatusFilter');
        $this->selectedIds = [];
    }

    /**
     * Öffnet den Bearbeitungsmodus für einen Mitarbeiter (sendet Event)
     */
    public function edit($id): void
    {
        // Just dispatch an event with the user ID
        $this->dispatch('edit-employee', ['userId' => $id]);
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $authCurrentTeamId = Auth::user()?->currentTeam?->id;

        $query = User::select([
            'users.id',
            'users.department_id',
            'users.name',
            'users.last_name',
            'users.phone_1',
            'users.email',
            'users.joined_at',
            'users.created_at',
            'users.model_status',
            'users.profile_photo_path',
            'users.slug',
            'users.company_id',
            'users.deleted_at',
        ])
            ->where('users.user_type', $this->userType)
            ->where('users.model_status', ModelStatus::ACTIVE->value)
            ->whereNull('users.deleted_at')
        ;

        $query->join('employees', 'users.id', '=', 'employees.user_id')
            ->join('team_user', function ($join) use ($authCurrentTeamId) {
                $join->on('users.id', '=', 'team_user.user_id')
                    ->where('team_user.team_id', '=', $authCurrentTeamId);
            });

        $query->with([
            'employee' => function ($q_employee) {
                $q_employee->select(['id', 'user_id', 'employee_status', 'profession_id', 'stage_id']);
            },

            'employee.profession:id,name',
            'employee.stage:id,name',
            'department:id,name',

            'roles' => function ($q_roles) {
                $q_roles->where('visible', RoleVisibility::Visible->value)
                    ->select('roles.id', 'roles.name');
            },
        ]);


        $this->applySearch($query);
        $this->applySorting($query);


        if ($this->statusFilter && $this->statusFilter !== ModelStatus::ACTIVE->value) {
            $query->where('users.model_status', $this->statusFilter);
        }

        if ($this->employeeStatusFilter) {
            $query->where('employees.employee_status', $this->employeeStatusFilter);
        }

        // Entfernt Duplikate, die durch JOINs entstehen könnten
        $query->distinct();


        $users = $query->simplePaginate(
            $this->perPage,
            ['users.*'],
            'page'
        );


        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => ModelStatus::cases(),
            'employeeStatuses' => EmployeeStatus::cases(),
        ]);
    }

//    public function placeholder():View
//    {
//        return view('livewire.placeholders.employee.index');
//    }
}
