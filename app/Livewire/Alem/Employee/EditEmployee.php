<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\Spatie\Role;
use App\Models\Team;
use App\Models\User;
use App\Traits\Model\WithModelStatusOptions;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Barryvdh\Debugbar\Facades\Debugbar;

class EditEmployee extends Component
{
    use AuthorizesRequests, WithModelStatusOptions;

    // Modal state
    public $showModal = false;
    private $dataLoaded = false;
    private $userLoaded = false;

    // User identification
    public ?int $userId = null;

    // Form fields - User data
    public $gender;
    public $name;
    public $last_name;
    public $email;
    public ?Carbon $joined_at = null;
    public $department = null;
    public $selectedTeams = [];
    public $selectedRoles = [];
    public $model_status;

    // Form fields - Employee data
    public $employee_status;
    public $profession;
    public $stage;
    public $supervisor = null;

    // Cached data collections
    private $teams = null;
    private $departments = null;
    private $roles = null;
    private $professions = null;
    private $stages = null;
    private $supervisors = null;

    /**
     * Event handler for edit-employee event
     */
    #[On('edit-employee')]
    public function loadUser($data): void
    {
        $this->loadEmployee($data['userId']);
        $this->modal('edit-employee')->show();
    }
//    public function loadUser(int $id): void
//    {
//        $this->userId = $id;
//        $this->showModal = true;
//
//        // Reset form state
//        $this->userLoaded = false;
//        $this->dataLoaded = false;
//
//        // Only load user data - load dependencies on render
//        $this->loadUserData();
//
//        // Trigger the modal to open
//        $this->dispatch('modal-show', ['name' => 'edit-employee']);
//    }

    /**
     * Load user and employee data with optimized query
     */
    protected function loadEmployee($id): void
    {
//        if ($this->userLoaded || !$this->userId) {
//            return;
//        }

        try {
            // Fetch user with only necessary relations and fields
            $user = User::select([
                'id', 'name', 'last_name', 'email', 'gender',
                'model_status', 'joined_at', 'department_id'
            ])
                ->with([
                    'employee:id,user_id,employee_status,profession_id,stage_id,supervisor_id',
                    'teams:id,name',
                    'roles:id,name'
                ])
                ->findOrFail($id);

            // Populate user fields
            $this->name = $user->name;
            $this->last_name = $user->last_name;
            $this->email = $user->email;
            $this->gender = $user->gender;
            $this->model_status = $user->model_status;
            $this->joined_at = $user->joined_at;
            $this->department = $user->department_id;

            // Teams and roles
            $this->selectedTeams = $user->teams->pluck('id')->toArray();
            $this->selectedRoles = $user->roles->pluck('id')->toArray();

            // Employee data
            if ($user->employee) {
                $this->employee_status = $user->employee->employee_status;
                $this->profession = $user->employee->profession_id;
                $this->stage = $user->employee->stage_id;
                $this->supervisor = $user->employee->supervisor_id;
            }

//            $this->userLoaded = true;
        } catch (\Exception $e) {
            Debugbar::error('Error loading user data: ' . $e->getMessage());
            Flux::toast(
                text: __('Error loading employee: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
            $this->closeModal();
        }
    }

    /**
     * Load essential dropdown data (lazy)
     */
    private function loadEssentialData(): void
    {
        if ($this->dataLoaded) {
            return;
        }

        try {
            $currentTeamId = auth()->user()->current_team_id;
            $currentCompanyId = auth()->user()->company_id;

            // Teams
            if ($this->teams === null) {
                $this->teams = Team::where('company_id', $currentCompanyId)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            // Departments filtered by team
            if ($this->departments === null) {
                $teamId = !empty($this->selectedTeams) ? $this->selectedTeams[0] : $currentTeamId;

                $query = Department::where('model_status', ModelStatus::ACTIVE->value)
                    ->where('company_id', $currentCompanyId);

                if ($teamId) {
                    $query->where('team_id', $teamId);
                }

                $this->departments = $query->select(['id', 'name'])->get();
            }

            // Roles
            if ($this->roles === null) {
                $this->roles = Role::where('access', RoleHasAccessTo::EmployeePanel->value)
                    ->where('visible', RoleVisibility::Visible->value)
                    ->where(function ($query) {
                        $query->where('created_by', 1)
                            ->orWhere('created_by', auth()->id());
                    })
                    ->select(['id', 'name', 'is_manager'])
                    ->get();
            }

            // Professions
            if ($this->professions === null) {
                $this->professions = Profession::where('company_id', $currentCompanyId)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            // Stages
            if ($this->stages === null) {
                $this->stages = Stage::where('company_id', $currentCompanyId)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            // Supervisors (only get users with manager role)
            if ($this->supervisors === null) {
                $this->supervisors = User::select(['users.id', 'users.name', 'users.last_name', 'users.profile_photo_path'])
                    ->join('model_has_roles', function ($join) {
                        $join->on('users.id', '=', 'model_has_roles.model_id')
                            ->where('model_has_roles.model_type', User::class);
                    })
                    ->join('roles', function ($join) {
                        $join->on('model_has_roles.role_id', '=', 'roles.id')
                            ->where('roles.is_manager', true);
                    })
                    ->where('users.company_id', $currentCompanyId)
                    ->where('users.id', '!=', $this->userId) // Exclude self from supervisors
                    ->whereNull('users.deleted_at')
                    ->orderBy('users.name')
                    ->distinct()
                    ->get();
            }

            $this->dataLoaded = true;
        } catch (\Exception $e) {
            Debugbar::error('Error loading dropdown data: ' . $e->getMessage());
        }
    }

    /**
     * Lifecycle hook to ensure data is loaded after validation errors
     */
    public function hydrate()
    {
        if ($this->showModal) {
            if (!$this->userLoaded && $this->userId) {
                $this->loadUserData();
            }

            if (!$this->dataLoaded) {
                $this->loadEssentialData();
            }
        }
    }

    /**
     * Property updated hook
     */
    public function updated($propertyName)
    {
        if ($propertyName === 'showModal' && $this->showModal) {
            if (!$this->userLoaded && $this->userId) {
                $this->loadUserData();
            }

            if (!$this->dataLoaded) {
                $this->loadEssentialData();
            }
        }

        if ($propertyName === 'selectedTeams') {
            // Reset departments when teams change
            $this->departments = null;
        }
    }

    /**
     * Update employee data
     */
    public function updateEmployee(): void
    {
        // Validate data
//        $this->validate([
//            // User validation rules
//            'gender' => 'required|string',
//            'name' => 'required|string|min:3',
//            'last_name' => 'required|string|min:3',
//            'email' => 'required|email|unique:users,email,'.$this->userId,
//            'model_status' => 'required|string',
//            'joined_at' => 'nullable|date',
//            'department' => 'required|exists:departments,id',
//
//            // Teams and roles validation
//            'selectedTeams' => 'required|array|min:1',
//            'selectedTeams.*' => 'exists:teams,id',
//            'selectedRoles' => 'required|array|min:1',
//            'selectedRoles.*' => 'exists:roles,id',
//
//            // Employee validation rules
//            'employee_status' => 'required|string',
//            'profession' => 'required|exists:professions,id',
//            'stage' => 'required|exists:stages,id',
//            'supervisor' => 'nullable|exists:users,id',);

        try {
            DB::beginTransaction();

            // 1. Update user
            $user = User::findOrFail($this->userId);

            $user->update([
                'gender' => $this->gender,
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'model_status' => $this->model_status,
                'joined_at' => $this->joined_at,
                'department_id' => $this->department,
            ]);

            // 2. Update roles
            $user->roles()->sync($this->selectedRoles);

            // 3. Update teams
            $user->teams()->sync($this->selectedTeams);

            // 4. Update employee record
            if ($user->employee) {
                $user->employee->update([
                    'employee_status' => $this->employee_status,
                    'profession_id' => $this->profession,
                    'stage_id' => $this->stage,
                    'supervisor_id' => $this->supervisor,
                ]);
            }

            DB::commit();

            // Show success message
            Flux::toast(
                text: __('Employee updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

            // Close modal and dispatch event to refresh table
            $this->closeModal();
            $this->dispatch('employee-updated');

        } catch (AuthorizationException $ae) {
            DB::rollBack();
            Flux::toast(
                text: __('You are not authorized to update this employee.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Debugbar::error('Error updating employee: ' . $e->getMessage());
            Flux::toast(
                text: __('Error updating employee: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Close modal and reset state
     */
    public function closeModal(): void
    {
        $this->reset([
            'userId', 'name', 'last_name', 'email', 'gender',
            'model_status', 'joined_at', 'department', 'selectedTeams',
            'selectedRoles', 'employee_status', 'profession', 'stage', 'supervisor'
        ]);

        $this->showModal = false;
        $this->userLoaded = false;
        $this->dataLoaded = false;

        // Reset cached collections
        $this->departments = null;

        // Close modal via dispatch
        $this->dispatch('modal-close', ['name' => 'edit-employee']);
    }

    /**
     * Property getters for dropdown data
     */
    public function getTeamsProperty()
    {
        if ($this->showModal && $this->teams === null) {
            $this->loadEssentialData();
        }
        return $this->teams ?? collect();
    }

    public function getDepartmentsProperty()
    {
        if ($this->showModal && $this->departments === null) {
            $this->loadEssentialData();
        }
        return $this->departments ?? collect();
    }

    public function getRolesProperty()
    {
        if ($this->showModal && $this->roles === null) {
            $this->loadEssentialData();
        }
        return $this->roles ?? collect();
    }

    public function getProfessionsProperty()
    {
        if ($this->showModal && $this->professions === null) {
            $this->loadEssentialData();
        }
        return $this->professions ?? collect();
    }

    public function getStagesProperty()
    {
        if ($this->showModal && $this->stages === null) {
            $this->loadEssentialData();
        }
        return $this->stages ?? collect();
    }

    public function getSupervisorsProperty()
    {
        if ($this->showModal && $this->supervisors === null) {
            $this->loadEssentialData();
        }
        return $this->supervisors ?? collect();
    }

    /**
     * Get employee status options
     */
    public function getEmployeeStatusOptionsProperty()
    {
        static $options = null;

        if ($options === null) {
            $options = collect(EmployeeStatus::cases())->map(function ($status) {
                return [
                    'value' => $status->value,
                    'label' => $status->label(),
                    'colors' => $status->colors(),
                    'icon' => $status->icon(),
                ];
            })->toArray();
        }

        return $options;
    }

    /**
     * Render component
     */
    public function render(): View
    {
        // Lazy load data when modal is shown
        if ($this->showModal) {
            if (!$this->userLoaded && $this->userId) {
                $this->loadUserData();
            }

            if (!$this->dataLoaded) {
                $this->loadEssentialData();
            }
        }

        return view('livewire.alem.employee.edit', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }
}
