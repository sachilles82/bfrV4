<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Employee;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Barryvdh\Debugbar\Facades\Debugbar;

class CreateEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee, WithModelStatusOptions;

    // Modal state
    public $showModal = false;

    // Controls data loading
    private $dataLoaded = false;

    public ?int $userId = null;
    public $selectedRoles = [];
    public $model_status;
    public $employee_status;
    public $invitations = true;

    // User Fields
    public $gender;
    public $name;
    public $last_name;
    public $email;
    public $email_verified_at;
    public $password;
    public ?Carbon $joined_at = null;
    public $department = null;
    public $selectedTeams = [];

    // Employee Fields
    public $profession;
    public $stage;
    public $supervisor = null;

    // Lazy loaded data containers
    private $professions = null;
    private $stages = null;
    private $roles = null;
    private $teams = null;
    private $departments = null;
    private $supervisors = null;


    /**
     * Lifecycle hook: wird aufgerufen, wenn das Modal geöffnet wird
     */
    #[On('modal-show')]
    public function loadData(): void
    {
        $this->setDefaultValues();
        $this->loadEssentialData();
        $this->dataLoaded = true;
    }

    /**
     * Set default values for form fields
     */
    private function setDefaultValues(): void
    {
        $this->gender = Gender::Male->value;
        $this->model_status = ModelStatus::ACTIVE->value;
        $this->employee_status = EmployeeStatus::PROBATION->value;
        $this->invitations = true;
    }


    /**
     * Load essential data for dropdowns
     */
    private function loadEssentialData(): void
    {
        try {
            $currentTeamId = auth()->user()->current_team_id;
            $currentCompanyId = auth()->user()->company_id;

            $this->teams = Team::where('company_id', $currentCompanyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $this->departments = Department::where('model_status', ModelStatus::ACTIVE->value)
                ->where('company_id', $currentCompanyId)
                ->where('team_id', $currentTeamId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $this->roles = Role::where(function ($query) {
                $query->where('access', RoleHasAccessTo::EmployeePanel)
                    ->where('visible', RoleVisibility::Visible);
            })
                ->where(function ($query) {
                    $query->where('created_by', 1)
                        ->orWhere('created_by', auth()->id());
                })
                ->select(['id', 'name', 'is_manager'])
                ->get();

            $this->professions = Profession::where('company_id', $currentCompanyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $this->stages = Stage::where('company_id', $currentCompanyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $this->supervisors = User::query()
                ->select(['users.id', 'users.name', 'users.last_name', 'users.profile_photo_path'])
                ->join('model_has_roles', function($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', User::class);
                })
                ->join('roles', function($join) {
                    $join->on('model_has_roles.role_id', '=', 'roles.id')
                        ->where('roles.is_manager', true);
                })
                ->where('users.company_id', $currentCompanyId)
                ->whereNull('users.deleted_at')
                ->orderBy('users.name')
                ->distinct()
                ->get();

        } catch (\Exception $e) {
            Debugbar::error('Error loading essential data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Event-Handler für profession-updated ohne Parameter
     */
    #[On('profession-updated')]
    public function refreshProfessions(): void
    {
        Debugbar::info('Refreshing professions after update');
        $this->professions = null; // Reset cached data to force reload
    }

    /**
     * Gets professions list
     */
    public function getProfessionsProperty()
    {
        // Keine Daten laden, wenn das Modal nicht geöffnet ist
        if (!$this->showModal) {
            return collect();
        }

        if ($this->professions === null) {
            try {
                $this->professions = Profession::where('company_id', auth()->user()->company_id)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            } catch (\Exception $e) {
                Debugbar::error('Error loading professions', ['error' => $e->getMessage()]);
                $this->professions = collect();
            }
        }

        return $this->professions;
    }

    /**
     * Event-Handler für stage-updated ohne Parameter
     */
    #[On('stage-updated')]
    public function refreshStages(): void
    {
        Debugbar::info('Refreshing stages after update');
        $this->stages = null; // Reset cached data to force reload
    }

    /**
     * Gets stages list
     */
    public function getStagesProperty()
    {
        // Keine Daten laden, wenn das Modal nicht geöffnet ist
        if (!$this->showModal) {
            return collect();
        }

        if ($this->stages === null) {
            try {
                $this->stages = Stage::where('company_id', auth()->user()->company_id)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            } catch (\Exception $e) {
                Debugbar::error('Error loading stages', ['error' => $e->getMessage()]);
                $this->stages = collect();
            }
        }

        return $this->stages;
    }

    /**
     * Gets roles list
     */
    public function getRolesProperty()
    {
        // Keine Daten laden, wenn das Modal nicht geöffnet ist
        if (!$this->showModal) {
            return collect();
        }

        if ($this->roles === null) {
            try {
                $this->roles = Role::where(function ($query) {
                    $query->where('access', RoleHasAccessTo::EmployeePanel)
                        ->where('visible', RoleVisibility::Visible);
                })
                    ->where(function ($query) {
                        $query->where('created_by', 1)
                            ->orWhere('created_by', auth()->id());
                    })
                    ->select(['id', 'name', 'is_manager'])
                    ->get();
            } catch (\Exception $e) {
                Debugbar::error('Error loading roles', ['error' => $e->getMessage()]);
                $this->roles = collect();
            }
        }

        return $this->roles;
    }

    /**
     * Gets teams list
     */
    public function getTeamsProperty()
    {
        // Keine Daten laden, wenn das Modal nicht geöffnet ist
        if (!$this->showModal) {
            return collect();
        }

        if ($this->teams === null) {
            try {
                $this->teams = Team::where('company_id', auth()->user()->company_id)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            } catch (\Exception $e) {
                Debugbar::error('Error loading teams', ['error' => $e->getMessage()]);
                $this->teams = collect();
            }
        }

        return $this->teams;
    }

    /**
     * Reset departments when teams change
     */
    public function updatedSelectedTeams()
    {
        $this->departments = null;
        $this->department = null; // Reset selected department
    }

    /**
     * Event-Handler für department-created ohne Parameter
     */
    #[On('department-created')]
    public function refreshDepartments(): void
    {
        Debugbar::info('Refreshing departments after update');
        $this->departments = null; // Reset cached data to force reload
    }

    /**
     * Gets departments filtered by selected team
     */
    public function getDepartmentsProperty()
    {
        // Keine Daten laden, wenn das Modal nicht geöffnet ist
        if (!$this->showModal) {
            return collect();
        }

        if ($this->departments === null) {
            try {
                $teamId = !empty($this->selectedTeams) ? $this->selectedTeams[0] : null;

                $query = Department::where('model_status', ModelStatus::ACTIVE->value)
                    ->where('company_id', auth()->user()->company_id);

                if ($teamId) {
                    $query->where('team_id', $teamId);
                }

                $this->departments = $query->select(['id', 'name'])->get();
            } catch (\Exception $e) {
                Debugbar::error('Error loading departments', ['error' => $e->getMessage()]);
                $this->departments = collect();
            }
        }

        return $this->departments;
    }

    /**
     * Gets supervisors list
     */
    public function getSupervisorsProperty()
    {
        // Keine Daten laden, wenn das Modal nicht geöffnet ist
        if (!$this->showModal) {
            return collect();
        }

        if ($this->supervisors === null) {
            try {
                $this->supervisors = User::query()
                    ->select(['users.id', 'users.name', 'users.last_name', 'users.profile_photo_path'])
                    ->join('model_has_roles', function($join) {
                        $join->on('users.id', '=', 'model_has_roles.model_id')
                            ->where('model_has_roles.model_type', User::class);
                    })
                    ->join('roles', function($join) {
                        $join->on('model_has_roles.role_id', '=', 'roles.id')
                            ->where('roles.is_manager', true);
                    })
                    ->where('users.company_id', auth()->user()->company_id)
                    ->whereNull('users.deleted_at')
                    ->orderBy('users.name')
                    ->distinct()
                    ->get();
            } catch (\Exception $e) {
                Debugbar::error('Error loading supervisors', ['error' => $e->getMessage()]);
                $this->supervisors = collect();
            }
        }

        return $this->supervisors;
    }

    /**
     * Get employee status options
     */
    public function getEmployeeStatusOptionsProperty()
    {
        $statuses = [];

        foreach (EmployeeStatus::cases() as $status) {
            $statuses[] = [
                'value' => $status->value,
                'label' => $status->label(),
                'colors' => $status->colors(),
                'icon' => $status->icon(),
            ];
        }
        return $statuses;
    }

    /**
     * Save employee
     */
    public function saveEmployee(): void
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // 1. Create user
            $user = User::create([
                'gender' => $this->gender,
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'email_verified_at' => now(),
                'department_id' => $this->department,
                'joined_at' => $this->joined_at ? Carbon::parse($this->joined_at) : null,
                'model_status' => $this->model_status,
                'user_type' => UserType::Employee,
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
            ]);

            // 2. Assign roles
            if (!empty($this->selectedRoles)) {
                $user->roles()->sync($this->selectedRoles);
            }

            // 3. Create employee record
            Employee::create([
                'user_id' => $user->id,
                'uuid' => (string) Str::uuid(),
                'profession_id' => $this->profession,
                'stage_id' => $this->stage,
                'employee_status' => $this->employee_status,
                'supervisor_id' => $this->supervisor,
            ]);

            // 4. Assign teams
            if (!empty($this->selectedTeams)) {
                $teamsWithRole = [];
                foreach ($this->selectedTeams as $teamId) {
                    $teamsWithRole[$teamId] = ['role' => 'member'];
                }

                if (!empty($teamsWithRole)) {
                    $user->teams()->attach($teamsWithRole);
                }
            } else {
                $user->teams()->attach(auth()->user()->currentTeam, ['role' => 'member']);
            }

            // 5. Send invitation (if enabled)
            if ($this->invitations) {
                // TODO: Email notification implementation
            }

            DB::commit();

            $this->resetForm();

            Flux::toast(
                text: __('Employee created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('employee-created');

        } catch (AuthorizationException $ae) {
            DB::rollBack();
            Flux::toast(
                text: __('You are not authorized to create employees.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Flux::toast(
                text: __('Error: ').$e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Reset form and close modal
     */
    public function resetForm(): void
    {
        $this->reset([
            'name', 'last_name', 'email', 'password', 'gender', 'selectedRoles',
            'joined_at', 'profession', 'stage', 'selectedTeams', 'supervisor',
            'model_status', 'employee_status', 'invitations',
        ]);

        // Close modal properly using Flux's modal API
        $this->dispatch('modal-close', ['name' => 'create-employee']);
        $this->showModal = false;

        // Reset to default values
        $this->setDefaultValues();
    }

    /**
     * Render component
     */
    public function render(): View
    {
        return view('livewire.alem.employee.create', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }
}
