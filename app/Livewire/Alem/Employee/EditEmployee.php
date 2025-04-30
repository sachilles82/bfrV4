<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Employee;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\User;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Models\Spatie\Role;
use App\Models\Team;
use App\Traits\Model\WithModelStatusOptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Illuminate\Support\Collection;

#[Lazy(isolate: false)]
class EditEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee, WithModelStatusOptions;

    // Modal state
    public bool $showEditModal = false;
    public bool $dataLoadedEdit = false;

    // User identification
    #[Locked]
    public ?int $userId = null;
    public ?User $user = null;

    // Eigenschaften für vorgeladene Daten - diese werden automatisch von Livewire befüllt
    public ?int $authUserId = null;
    public ?int $currentTeamId = null;
    public ?int $companyId = null;

    // User form fields
    public ?string $name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $gender = null;
    public ?ModelStatus $model_status = null;
    public $joined_at = null;
    public $department = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    // Employee fields
    public ?EmployeeStatus $employee_status = null;
    public $profession = null;
    public $stage = null;
    public $supervisor = null;

    // Optimierung: Cache-Eigenschaften privat lassen und initialisieren
    private ?Collection $teams = null;
    private ?Collection $departments = null;
    private ?Collection $roles = null;
    private ?Collection $professions = null;
    private ?Collection $stages = null;
    private ?Collection $supervisors = null;

    #[On('open-edit-modal')]
    public function openEditEmployeeModal($userId): void
    {

        // $this->authorize('update', User::class);

        $this->userId = $userId;

        $this->loadRelationForDropDowns();

        // Kein Join in der Edit und Create verwenden. Nur in der Table ist es sinnvoll
        $this->user = User::with([
            'employee:id,user_id,employee_status,profession_id,stage_id,supervisor_id',
            'teams:id,name',
            'roles:id,name',
            'department:id,name'
        ])->findOrFail($this->userId);

        $this->loadEmployeeData();

        $this->showEditModal = true;
        $this->dataLoadedEdit = false;

    }

    /**
     * Fill form with user data
     */
    protected function loadEmployeeData(): void
    {
        if (!$this->user) return;

        $this->gender = $this->user->gender;
        $this->name = $this->user->name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;

        $this->selectedTeams = $this->user->teams->pluck('id')->toArray();
        $this->department = $this->user->department_id;

        // ModelStatus ENUM
        $this->model_status = $this->user->model_status;
        $this->joined_at = $this->user->joined_at;


        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();

        if ($employee = $this->user->employee) {
            // Employee Status ENUM
            $this->employee_status = $employee->employee_status;

            $this->profession = $employee->profession_id;
            $this->stage = $employee->stage_id;
            $this->supervisor = $employee->supervisor_id;
        }
    }


    protected function loadRelationForDropDowns(): void
    {
        if (!$this->showEditModal || $this->dataLoadedEdit) {
            return;
        }

        try {
            $companyId = $this->companyId;
            $teamId = $this->currentTeamId;

            // Teams laden mit Caching
            if ($this->teams === null) {
                $this->teams = Team::getCompanyTeams($companyId);
            }

            // Departments laden mit Caching
            if ($this->departments === null) {
                $this->departments = Department::getDepartmentsForTeam($teamId);
            }

            // Supervisors laden mit Caching
            if ($this->supervisors === null) {
                $this->supervisors = User::getCompanyManagers($companyId);
            }

            // Roles laden mit Caching
            if ($this->roles === null) {
                $this->roles = Role::getEmployeePanelRoles($companyId);
            }

            // Professions laden mit Caching
            if ($this->professions === null) {
                $this->professions = Profession::getCompanyProfessions($companyId);
            }

            // Stages laden mit Caching
            if ($this->stages === null) {
                $this->stages = Stage::getCompanyStages($companyId);
            }

            $this->dataLoadedEdit = true;

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('An error occurred while loading the employee Relation Data.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }


    /**
     * Update employee in database
     */
    public function updateEmployee(): void
    {
        try {

            $this->validate();

            DB::beginTransaction();

            User::where('id', $this->userId)->update([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'gender' => $this->gender,
                'model_status' => $this->model_status,
                'joined_at' => $this->joined_at ? Carbon::parse($this->joined_at)->format('Y-m-d') : null,
                'department_id' => $this->department,
            ]);

            $employee = Employee::where('user_id', $this->userId)->first();

            $employee->update([
                'employee_status' => $this->employee_status,
                'profession_id' => $this->profession,
                'stage_id' => $this->stage,
                'supervisor_id' => $this->supervisor,
            ]);

            $updatedUser = User::find($this->userId);

            $updatedUser->roles()->sync($this->selectedRoles);
            $updatedUser->teams()->sync($this->selectedTeams);

            DB::commit();

            $this->finish();

            $this->dispatch('employee-updated');

            Flux::toast(
                text: __('Employee Profile updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('An error occurred while saving the employee.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }


    // ---  Wenn einer dieser Models refresh wird, sollen die Daten neubefüllt werden
    #[On('profession-updated')]
    public function refreshProfessions(): void
    {
        $this->professions = null;
        // Neuer Cache-Ansatz für Profession-Updates
        Profession::flushCompanyCache($this->companyId);
        if ($this->showEditModal) $this->loadRelationForDropDowns();
    }

    #[On('stage-updated')]
    public function refreshStages(): void
    {
        $this->stages = null;
        // Neuer Cache-Ansatz für Stage-Updates
        Stage::flushCompanyCache($this->companyId);
        if ($this->showEditModal) $this->loadRelationForDropDowns();
    }

    #[On(['department-created', 'department-updated'])]
    public function refreshDepartments(): void
    {
        $this->departments = null;
        // Durch den neuen Team-basierten Ansatz für Departments
        Department::flushTeamCache($this->currentTeamId);
        if ($this->showEditModal) $this->loadRelationForDropDowns();
    }

    /**
     * Get employee status options for dropdown - static caching
     */
    public function getEmployeeStatusOptionsProperty(): array
    {
        static $options = null;
        if ($options === null) {
            $options = collect(EmployeeStatus::cases())->map(fn($status) => [
                'value' => $status->value,
                'label' => $status->label(),
                'colors' => $status->colors(),
                'icon' => $status->icon(),
            ])->toArray();
        }
        return $options;
    }

    /**
     * Close modal and reset state
     */
    public function finish(): void
    {

        $this->reset([
            'userId', 'user', 'showEditModal', 'dataLoadedEdit',
            'name', 'last_name', 'email', 'gender',
            'model_status', 'joined_at', 'department',
            'selectedTeams', 'selectedRoles',
            'employee_status', 'profession', 'stage', 'supervisor'
        ]);

        $this->resetValidation();

        // Reset cached data (private properties)
        $this->teams = null;
        $this->departments = null;
        $this->roles = null;
        $this->professions = null;
        $this->stages = null;
        $this->supervisors = null;

        $this->modal('edit-employee')->close();
    }

    #[Computed]
    public function teams(): Collection
    {
        return $this->teams ?? collect();
    }

    #[Computed]
    public function departments(): Collection
    {
        return $this->departments ?? collect();
    }

    #[Computed]
    public function roles(): Collection
    {
        return $this->roles ?? collect();
    }

    #[Computed]
    public function professions(): Collection
    {
        return $this->professions ?? collect();
    }

    #[Computed]
    public function stages(): Collection
    {
        return $this->stages ?? collect();
    }

    #[Computed]
    public function supervisors(): Collection
    {
        return $this->supervisors ?? collect();
    }

    public function render(): View
    {
        return view('livewire.alem.employee.edit', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }
}
