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
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Barryvdh\Debugbar\Facades\Debugbar;

#[Lazy(isolate: false)]
class CreateEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee, WithModelStatusOptions;

    // Modal state
    public $showModal = false;
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

    // Lazy-loaded data containers (private properties)
    private $teams = null;
    private $departments = null;
    private $roles = null;
    private $professions = null;
    private $stages = null;
    private $supervisors = null;

    /**
     * Lifecycle hook: wird aufgerufen, wenn das Modal geöffnet wird
     */
    #[On('modal-show')]
    public function loadData(): void
    {
        $this->setDefaultValues();
        $this->loadEssentialData();
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
     * Load essential data for dropdowns using batched queries in a transaction
     */
    private function loadEssentialData(): void
    {
        try {
            // Store user data once to avoid multiple auth() calls
            $user = auth()->user();
            $currentTeamId = $user->current_team_id;
            $currentCompanyId = $user->company_id;
            $currentUserId = $user->id;

            // Use a transaction to batch all queries together
            DB::transaction(function() use ($currentTeamId, $currentCompanyId, $currentUserId) {
                // Only run queries for data we haven't loaded yet
                if ($this->teams === null) {
                    $this->teams = Team::where('company_id', $currentCompanyId)
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();
                }

                if ($this->departments === null) {
                    $this->departments = Department::where('model_status', ModelStatus::ACTIVE->value)
                        ->where('company_id', $currentCompanyId)
                        ->where('team_id', $currentTeamId)
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();
                }

                if ($this->roles === null) {
                    $this->roles = Role::where('access', RoleHasAccessTo::EmployeePanel->value)
                        ->where('visible', RoleVisibility::Visible->value)
                        ->where(function ($query) use ($currentUserId) {
                            $query->where('created_by', 1)
                                ->orWhere('created_by', $currentUserId);
                        })
                        ->select(['id', 'name', 'is_manager'])
                        ->get();
                }

                if ($this->professions === null) {
                    $this->professions = Profession::where('company_id', $currentCompanyId)
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();
                }

                if ($this->stages === null) {
                    $this->stages = Stage::where('company_id', $currentCompanyId)
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();
                }

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
                        ->whereNull('users.deleted_at')
                        ->orderBy('users.name')
                        ->distinct()
                        ->get();
                }
            }, 3); // Using lower isolation level (3) for better read performance

            $this->dataLoaded = true;
        } catch (\Exception $e) {
            Debugbar::error('Error loading essential data: ' . $e->getMessage());
        }
    }

    /**
     * Lifecycle hook to make sure data is loaded even after validation errors
     */
    public function hydrate(): void
    {
        if ($this->showModal && !$this->dataLoaded) {
            $this->loadEssentialData();
        }
    }

    /**
     * Event-Handler für profession-updated
     */
    #[On('profession-updated')]
    public function refreshProfessions(): void
    {
        $this->professions = null;
    }

    /**
     * Event-Handler für stage-updated
     */
    #[On('stage-updated')]
    public function refreshStages(): void
    {
        $this->stages = null;
    }

    /**
     * Event-Handler für department-created
     */
    #[On('department-created')]
    public function refreshDepartments(): void
    {
        $this->departments = null;
    }

    /**
     * This method is called when properties are updated
     */
    public function updated($propertyName)
    {
        if ($propertyName === 'showModal' && $this->showModal && !$this->dataLoaded) {
            $this->loadEssentialData();
        }

        if ($propertyName === 'selectedTeams') {
            $this->departments = null;
            $this->department = null;
        }
    }

    /**
     * Gets professions list
     */
    public function getProfessionsProperty()
    {
        if ($this->showModal && $this->professions === null) {
            $this->loadEssentialData();
        }

        return $this->professions ?? collect();
    }

    /**
     * Gets stages list
     */
    public function getStagesProperty()
    {
        if ($this->showModal && $this->stages === null) {
            $this->loadEssentialData();
        }

        return $this->stages ?? collect();
    }

    /**
     * Gets roles list
     */
    public function getRolesProperty()
    {
        if ($this->showModal && $this->roles === null) {
            $this->loadEssentialData();
        }

        return $this->roles ?? collect();
    }

    /**
     * Gets teams list
     */
    public function getTeamsProperty()
    {
        if ($this->showModal && $this->teams === null) {
            $this->loadEssentialData();
        }

        return $this->teams ?? collect();
    }

    /**
     * Gets departments filtered by selected team
     */
    public function getDepartmentsProperty()
    {
        if ($this->showModal && $this->departments === null) {
            // Store auth user data to avoid multiple calls
            $user = auth()->user();
            $teamId = !empty($this->selectedTeams) ? $this->selectedTeams[0] : $user->current_team_id;
            $companyId = $user->company_id;

            $query = Department::where('model_status', ModelStatus::ACTIVE->value)
                ->where('company_id', $companyId)
                ->where('team_id', $teamId)
                ->select(['id', 'name']);

            $this->departments = $query->get();
        }

        return $this->departments ?? collect();
    }
    /**
     * Gets supervisors list
     */
    public function getSupervisorsProperty()
    {
        if ($this->showModal && $this->supervisors === null) {
            $this->loadEssentialData();
        }

        return $this->supervisors ?? collect();
    }

    /**
     * Get employee status options - use memoization for frequently accessed data
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
     * Save employee
     */
    public function saveEmployee(): void
    {
        if (!$this->dataLoaded) {
            $this->loadEssentialData();
        }

        // Sichere Passwortgenerierung
        $this->generatedPassword = $this->generateSecurePassword();

        $this->validate();

        $this->showModal = false;

        try {
            DB::beginTransaction();

            // 1. Create user
            $user = User::create([
                'gender' => $this->gender,
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->generatedPassword),
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
                'uuid' => (string)Str::uuid(),
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
                text: __('Error: ') . $e->getMessage(),
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

        $this->generatedPassword = null;

        $this->dataLoaded = false;

        // Reset to default values
        $this->setDefaultValues();
    }

    /**
     * Generiert ein sicheres, zufälliges Passwort mit Buchstaben, Zahlen und Sonderzeichen
     * Länge: 10 Zeichen, mindestens je ein Zeichen aus jeder Kategorie
     */
    private function generateSecurePassword(): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()_-+=<>?';

        // Mindestens ein Zeichen aus jeder Kategorie
        $password = [
            $lowercase[random_int(0, strlen($lowercase) - 1)],
            $uppercase[random_int(0, strlen($uppercase) - 1)],
            $numbers[random_int(0, strlen($numbers) - 1)],
            $special[random_int(0, strlen($special) - 1)],
        ];

        // Restliche Zeichen bis zur gewünschten Länge auffüllen
        $allChars = $lowercase . $uppercase . $numbers . $special;
        $passwordLength = random_int(8, 10); // Zufällige Länge zwischen 8 und 10

        for ($i = count($password); $i < $passwordLength; $i++) {
            $password[] = $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Reihenfolge durchmischen
        shuffle($password);

        return implode('', $password);
    }

    /**
     * Render component
     */
    public function render(): View
    {
        if ($this->showModal && !$this->dataLoaded) {
            $this->loadEssentialData();
        }

        return view('livewire.alem.employee.create', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }
}
