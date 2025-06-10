<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Department;
use App\Models\Alem\Employee;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
use App\Models\Spatie\Role;
use App\Models\Team;
use App\Models\User;
use App\Traits\Employee\EmployeeStatusOptions;
use App\Traits\Enum\GenderOptions;
use App\Traits\Model\ModelStatusOptions;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateEmployee extends Component
{
    use AuthorizesRequests;
    use ValidateEmployee, HandleCatchError;
    use ModelStatusOptions, EmployeeStatusOptions, GenderOptions;

    /** Modal-Status */
    public bool $showCreateModal = false;

    /** Eigenschaften für vorgeladene Daten */
    public ?int $authUserId = null;
    public ?int $currentTeamId = null;
    public ?int $companyId = null;

    /** Benutzer-Felder */
    public ?int $userId = null;
    public ?Gender $gender = null;
    public ?string $name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?ModelStatus $model_status = null;
    public ?Carbon $joined_at = null;
    public ?int $department = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    /** Mitarbeiter-Felder */
    public ?EmployeeStatus $employee_status = null;
    public $profession;
    public $stage;
    public ?int $supervisor = null;
    public bool $invitation = false;

    /**
     * Event Handler: Wird aufgerufen, wenn das Modal geöffnet werden soll
     * Lädt User-Daten nur bei Bedarf (Lazy Loading)
     */
    #[On('create-employee-modal')]
    public function openCreateEmployeeModal(): void
    {
        $this->resetFormData();
        $this->gender = Gender::Male;
        $this->model_status = ModelStatus::ACTIVE;
        $this->employee_status = EmployeeStatus::PROBATION;
        $this->invitation = true;
        $this->showCreateModal = true;
    }

    /**
     * Führt alle notwendigen DB-Operationen in einer Transaktion aus
     */
    public function saveEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {

                $user = User::create([
                    'gender' => $this->gender,
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'password' => Hash::make(Str::password()),
                    'email_verified_at' => now(),
                    'department_id' => $this->department,
                    'joined_at' => $this->joined_at?->toDateString(),
                    'model_status' => $this->model_status,
                    'user_type' => UserType::Employee,
                    'company_id' => $this->companyId,
                    'created_by' => $this->authUserId,
                ]);

                $this->createEmployee($user);
                $this->assignTeams($user);
                $this->assignRoles($user);

                if ($this->invitation) {
                    // TODO: E-Mail-Benachrichtigung implementieren
                }
            });


            $this->closeCreateEmployeeModal();
            $this->dispatch('employee-created');

            Flux::toast(
                text: __('Employee created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * Private Save Helper-Methoden
     */
    private function createEmployee(User $user): void
    {
        Employee::create([
            'user_id' => $user->id,
            'profession_id' => $this->profession,
            'stage_id' => $this->stage,
            'employee_status' => $this->employee_status,
            'supervisor_id' => $this->supervisor,
        ]);
    }

    private function assignTeams(User $user): void
    {
        if (!empty($this->selectedTeams)) {
            $teamsWithRole = collect($this->selectedTeams)
                ->mapWithKeys(fn($teamId) => [$teamId => ['role' => 'member']])
                ->toArray();

            $user->teams()->attach($teamsWithRole);
        } else {
            // Nutze die übergebene $currentTeamId Property
            $user->teams()->attach($this->currentTeamId, ['role' => 'member']);
        }
    }

    private function assignRoles(User $user): void
    {
        if (!empty($this->selectedRoles)) {
            $user->roles()->sync($this->selectedRoles);
        }
    }

    /**
     * Event-Handler: Aktualisiert Profession-Auswahl
     */
    #[On(['profession-created', 'profession-updated', 'profession-deleted'])]
    public function refreshProfessions(?int $id = null): void
    {
        if ($id) {
            $this->profession = $id;
        }

        // Prüfe ob ausgewählte Profession noch existiert
        if ($this->profession && !$this->professions->contains('id', $this->profession)) {
            $this->profession = null;
        }
    }

    /**
     * Event-Handler: Aktualisiert Stage-Auswahl
     */
    #[On(['stage-created', 'stage-updated', 'stage-deleted'])]
    public function refreshStages(?int $id = null): void
    {
        if ($id) {
            $this->stage = $id;
        }

        // Prüfe ob ausgewählte Stage noch existiert
        if ($this->stage && !$this->stages->contains('id', $this->stage)) {
            $this->stage = null;
        }
    }

    /**
     * Event-Handler: Aktualisiert Department-Auswahl
     */
    #[On(['department-updated', 'department-created', 'department-deleted'])]
    public function refreshDepartments(?int $id = null): void
    {
        if ($id) {
            $this->department = $id;
        }

        // Prüfe ob ausgewähltes Department noch existiert
        if ($this->department && !$this->departments->contains('id', $this->department)) {
            $this->department = null;
        }
    }

    public function getTeamsProperty(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        return Team::getCompanyTeams($this->companyId);
    }

    public function getDepartmentsProperty(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        return Department::getDepartmentsForTeam($this->currentTeamId);
    }

    public function getRolesProperty(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        return Role::getEmployeePanelRoles($this->companyId);
    }

    public function getProfessionsProperty(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        return Profession::getCompanyProfessions($this->companyId);
    }

    public function getStagesProperty(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        return Stage::getCompanyStages($this->companyId);
    }

    public function getSupervisorsProperty(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        $supervisors = User::getCompanyManagers($this->companyId);

        // Filter aktuellen User raus
        $currentUserId = $this->userId ?? 0;
        return $supervisors->reject(function ($supervisor) use ($currentUserId) {
            return $supervisor && isset($supervisor->id) && $supervisor->id === $currentUserId;
        });
    }

    /**
     * Schließt das Modal und bereinigt alle Daten
     */
    public function closeCreateEmployeeModal(): void
    {
        $this->modal('create-employee')->close();
        $this->resetFormData();
        $this->showCreateModal = false;
    }

    private function resetFormData(): void
    {
        $this->resetErrorBag();
        $this->reset([
            'gender', 'name', 'last_name', 'email', 'selectedTeams',
            'department', 'supervisor', 'selectedRoles', 'profession',
            'stage', 'joined_at', 'employee_status', 'model_status',
            'invitation',
        ]);
    }

    public function render(): View
    {
        return view('livewire.alem.employee.create');
    }
}
