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
use App\Models\Spatie\Role;
use App\Models\User;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Traits\Model\WithModelStatusOptions;
use Illuminate\Support\Carbon;
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
    use ValidateEmployee, AuthorizesRequests,
        WithModelStatusOptions;

    public ?int $userId = null;                     //Wird für den Supervisor benötigt

    public $selectedRoles = [];
    public $model_status;
    public $employee_status;
    public $invitations = true;

    /**
     * Benutzer-Felder (User Fields)
     */
    public $gender;
    public $name;
    public $last_name;
    public $email;
    public $email_verified_at;
    public $password;
    public ?Carbon $joined_at = null;
    public $department = null;
    public $selectedTeams = [];

    /**
     * Mitarbeiter-spezifische Felder (Employee Fields)
     */
    public $profession;
    public $stage;
    public $supervisor = null;

    public function mount(): void
    {
        $this->gender = Gender::Male->value;
        $this->model_status = ModelStatus::ACTIVE->value;
        $this->employee_status = EmployeeStatus::PROBATION->value;
        $this->invitations = true;
    }

    #[On('profession-updated')]
    public function getProfessionsProperty()
    {
        return Profession::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    #[On('stage-updated')]
    public function getStagesProperty()
    {
        return Stage::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    public function getRolesProperty()
    {
        return Role::where(function ($query) {
            $query->where('access', RoleHasAccessTo::EmployeePanel)
                ->where('visible', RoleVisibility::Visible);
        })
            ->where('created_by', 1)                 // System-erstellte Rollen
            ->orWhere('created_by', auth()->id())    // Oder vom aktuellen Benutzer erstellte Rollen
            ->get();
    }

    public function getTeamsProperty()
    {
        return auth()->user()->allTeams();
    }

    #[On('department-created')]
    public function getDepartmentsProperty()
    {
        $teamId = !empty($this->selectedTeams) ? $this->selectedTeams[0] : null;

        $query = Department::where('model_status', ModelStatus::ACTIVE->value)
            ->where('company_id', auth()->user()->company_id);

        if ($teamId) {
            $query->where('team_id', $teamId);
        }

        return $query->get();
    }

    public function getSupervisorsProperty()
    {
        return User::query()
            ->select(['id', 'name', 'last_name', 'profile_photo_path'])
            ->where('company_id', auth()->user()->company_id)
            ->whereHas('roles', function ($query) {
                $query->where('is_manager', true);
            })
            ->orderBy('name')
            ->get();
    }
    /**
     * Gets all available employee status options with their labels, colors, and icons
     *
     * @return array
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

    public function saveEmployee(): void
    {
        $this->validate();

        try {
            // 1. Benutzer erstellen
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

            // 2. Rollen zuweisen
            if (!empty($this->selectedRoles)) {
                $user->roles()->sync($this->selectedRoles);
            }

            // 3. Mitarbeiter-Datensatz erstellen
            Employee::create([
                'user_id' => $user->id,
                'uuid' => (string)Str::uuid(),
                'profession_id' => $this->profession,
                'stage_id' => $this->stage,
                'employee_status' => $this->employee_status,
                'supervisor_id' => $this->supervisor,
            ]);

            // 4. Teams zuweisen
            if (!empty($this->selectedTeams)) {
                foreach ($this->selectedTeams as $teamId) {
                    $team = auth()->user()->allTeams()->find($teamId);
                    if ($team) {
                        $user->teams()->attach($team, ['role' => 'member']);
                    }
                }
            } else {
                $user->teams()->attach(auth()->user()->currentTeam, ['role' => 'member']);
            }

            // 5. Benachrichtigung senden (falls aktiviert)
            if ($this->invitations) {
                // Hier E-Mail-Benachrichtigung implementieren
                // Mail::to($this->email)->send(new EmployeeInvitation($user));
            }

            $this->resetForm();

            Flux::toast(
                text: __('Employee created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('employee-created');

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
            'name', 'last_name', 'email', 'password', 'gender', 'selectedRoles',
            'joined_at', 'profession', 'stage', 'selectedTeams', 'supervisor',
            'model_status', 'employee_status', 'invitations'
        ]);

        $this->modal('create-employee')->close();

        $this->model_status = ModelStatus::ACTIVE->value;
        $this->employee_status = EmployeeStatus::PROBATION->value;
        $this->gender = Gender::Male->value;
        $this->invitations = true;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.create', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }
}
