<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Employee\Employee;
use App\Models\Spatie\Role;
use App\Models\User;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateEmployee extends Component
{
    use ValidateEmployee, AuthorizesRequests;

    // User fields
    public $name;
    public $last_name;
    public $email;
    public $password;
    public $joined_at;
    public $gender = null;
    public $role = 'employee';
    public $selectedTeams = [];
    public $model_status = null;
    public $employee_status = null;

    // Employee fields
    public $profession;
    public $stage;

    // Notification settings
    public $notifications = false;
    public bool $isActive = false;

    // Modal status
    public bool $showModal = false;

    public function mount()
    {
        // Initialize model_status with ACTIVE value if null
        $this->model_status = ModelStatus::ACTIVE->value;
        $this->employee_status = EmployeeStatus::PROBATION->value;
    }

    #[On('professionUpdated')]
    public function getProfessionsProperty()
    {
        return Profession::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    #[On('stageUpdated')]
    public function getStagesProperty()
    {
        return Stage::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    public function getGendersProperty()
    {
        return collect(Gender::cases())->map(function ($gender) {
            return [
                'value' => $gender->value,
                'name' => $gender->name
            ];
        });
    }

    public function getEmployeeStatusesProperty()
    {
        return collect(EmployeeStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'name' => $status->name
            ];
        });
    }

    #[On('roleUpdated')]
    public function getRolesProperty()
    {
        return Role::where(function($query) {
            $query->where('access', RoleHasAccessTo::EmployeePanel->value)
                ->where('visible', RoleVisibility::Visible->value);
        })
            ->where('created_by', 1)
            ->orWhere('created_by', auth()->id())
            ->get();
    }

    public function getTeamsProperty()
    {
        return auth()->user()->allTeams();
    }

    public function saveEmployee(): void
    {
        $this->authorize('create', Employee::class);
        $this->validate();

        try {
            // Create new user
            $user = User::create([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'email_verified_at' => $this->isActive ? now() : null,
                'password' => Hash::make($this->password),
                'user_type' => UserType::Employee,
                'gender' => Gender::from($this->gender),
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
                'model_status' => $this->model_status ?? ModelStatus::ACTIVE->value,
                'joined_at' => $this->joined_at ? Carbon::parse($this->joined_at) : null,
            ]);

            // Assign role
            $user->assignRole($this->role);

            // Create employee record
            Employee::create([
                'user_id' => $user->id,
                'uuid' => (string)Str::uuid(),
                'date_hired' => $this->joined_at ? Carbon::parse($this->joined_at) : null,
                'profession' => $this->profession,
                'stage' => $this->stage,
                'company_id' => auth()->user()->company_id,
                'team_id' => auth()->user()->currentTeam->id,
                'created_by' => auth()->id(),
                'employee_status' => $this->employee_status ?? EmployeeStatus::PROBATION->value,
            ]);

            // Add user to selected teams
            if (!empty($this->selectedTeams)) {
                foreach ($this->selectedTeams as $teamId) {
                    $team = auth()->user()->allTeams()->find($teamId);
                    if ($team) {
                        $user->teams()->attach($team, ['role' => 'member']);
                    }
                }
            } else {
                // If no teams selected, at least add the user to the current team
                $user->teams()->attach(auth()->user()->currentTeam, ['role' => 'member']);
            }

            // Send notification email if toggled
            if ($this->notifications) {
                // Implement email notification logic here
                // Mail::to($this->email)->send(new EmployeeInvitation($user));
            }

            $this->resetForm();
            $this->dispatch('employeeCreated');

            Flux::toast(
                text: __('Employee created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (AuthorizationException $ae) {
            Flux::toast(
                text: __('You are not authorized to create employees.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'name', 'last_name', 'email', 'password', 'gender', 'role',
            'joined_at', 'profession', 'stage', 'selectedTeams',
            'model_status', 'employee_status', 'notifications', 'isActive'
        ]);
        $this->showModal = false;

        // Re-initialize default values
        $this->model_status = ModelStatus::ACTIVE->value;
        $this->employee_status = EmployeeStatus::PROBATION->value;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.create');
    }
}
