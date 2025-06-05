<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee;
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
    public bool $invitations = false;

    /**
     * PRIVATE Cache-Properties - werden NICHT von Livewire serialisiert
     */
    private ?Collection $cachedTeams = null;
    private ?Collection $cachedDepartments = null;
    private ?Collection $cachedRoles = null;
    private ?Collection $cachedProfessions = null;
    private ?Collection $cachedStages = null;
    private ?Collection $cachedSupervisors = null;

    /**
     * Lebenszyklusmethode: Wird aufgerufen, wenn das Modal geöffnet wird
     */
    #[On('create-employee-modal')]
    public function openCreateEmployeeModal(): void
    {
        $this->gender = Gender::Male;
        $this->model_status = ModelStatus::ACTIVE;
        $this->employee_status = EmployeeStatus::PROBATION;
        $this->invitations = true;
        $this->showCreateModal = true;
    }

    /**
     * Führt alle notwendigen DB-Operationen in einer Transaktion aus
     */
    public function saveEmployee(): void
    {
        $generatedPassword = Str::password();
        $this->validate();

        try {
            DB::beginTransaction();

            $user = User::create([
                'gender' => $this->gender,
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($generatedPassword),
                'email_verified_at' => now(),
                'department_id' => $this->department,
                'joined_at' => $this->joined_at?->toDateString(),
                'model_status' => $this->model_status,
                'user_type' => UserType::Employee,
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
            ]);

            if (!empty($this->selectedRoles)) {
                $user->roles()->sync($this->selectedRoles);
            }

            Employee::create([
                'user_id' => $user->id,
                'profession_id' => $this->profession,
                'stage_id' => $this->stage,
                'employee_status' => $this->employee_status,
                'supervisor_id' => $this->supervisor,
            ]);

            if (!empty($this->selectedTeams)) {
                $teamsWithRole = [];
                foreach ($this->selectedTeams as $teamId) {
                    $teamsWithRole[$teamId] = ['role' => 'member'];
                }
                $user->teams()->attach($teamsWithRole);
            } else {
                $user->teams()->attach(auth()->user()->currentTeam, ['role' => 'member']);
            }

            if ($this->invitations) {
                // TODO: E-Mail-Benachrichtigung implementieren
            }

            DB::commit();

            $this->closeCreateEmployeeModal();
            $this->dispatch('employee-created');

            Flux::toast(
                text: __('Employee created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error("Fehler beim Erstellen des Mitarbeiters: " . $e->getMessage(), [
                'exception' => $e,
                'acting_user_id' => $this->authUserId ?? auth()->id(),
                'formData' => collect($this->only([
                    'gender', 'name', 'last_name', 'email', 'model_status',
                    'joined_at', 'department', 'selectedTeams', 'selectedRoles',
                    'employee_status', 'profession', 'stage', 'supervisor', 'invitations'
                ]))->toArray()
            ]);

            Flux::toast(
                text: __('An error occurred while saving the employee.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }

    /**
     * Event-Handler: Aktualisiert Professions-Cache
     */
    #[On(['profession-created', 'profession-updated', 'profession-deleted'])]
    public function refreshProfessions(?int $id = null): void
    {
        $this->cachedProfessions = null;

        if ($id) {
            $this->profession = $id;
        }

        if ($this->profession && $this->professions() && !$this->professions()->contains('id', $this->profession)) {
            $this->profession = null;
        }
    }

    /**
     * Event-Handler: Aktualisiert Stages-Cache
     */
    #[On(['stage-created', 'stage-updated', 'stage-deleted'])]
    public function refreshStages(?int $id = null): void
    {
        $this->cachedStages = null;

        if ($id) {
            $this->stage = $id;
        }

        if ($this->stage && $this->stages() && !$this->stages()->contains('id', $this->stage)) {
            $this->stage = null;
        }
    }

    /**
     * Event-Handler: Aktualisiert Departments-Cache
     */
    #[On(['department-updated', 'department-created', 'department-deleted'])]
    public function refreshDepartments(?int $id = null): void
    {
        $this->cachedDepartments = null;

        if ($id) {
            $this->department = $id;
        }

        if ($this->department && $this->departments() && !$this->departments()->contains('id', $this->department)) {
            $this->department = null;
        }
    }

    /**
     * Computed Properties - laden Daten nur bei Bedarf
     */
    #[Computed]
    public function teams(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        if ($this->cachedTeams === null) {
            $this->cachedTeams = Team::getCompanyTeams($this->companyId);
        }

        return $this->cachedTeams;
    }

    #[Computed]
    public function departments(): Collection
    {
        if (!$this->showCreateModal || !$this->currentTeamId) {
            return collect();
        }

        if ($this->cachedDepartments === null) {
            $this->cachedDepartments = Department::getDepartmentsForTeam($this->currentTeamId);
        }

        return $this->cachedDepartments;
    }

    #[Computed]
    public function roles(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        if ($this->cachedRoles === null) {
            $this->cachedRoles = Role::getEmployeePanelRoles($this->companyId);
        }

        return $this->cachedRoles;
    }

    #[Computed]
    public function professions(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        if ($this->cachedProfessions === null) {
            $this->cachedProfessions = Profession::getCompanyProfessions($this->companyId);
        }

        return $this->cachedProfessions;
    }

    #[Computed]
    public function stages(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        if ($this->cachedStages === null) {
            $this->cachedStages = Stage::getCompanyStages($this->companyId);
        }

        return $this->cachedStages;
    }

    #[Computed]
    public function supervisors(): Collection
    {
        if (!$this->showCreateModal || !$this->companyId) {
            return collect();
        }

        if ($this->cachedSupervisors === null) {
            $this->cachedSupervisors = User::getCompanyManagers($this->companyId);
        }

        // Filter aktuellen User raus
        $currentUserId = $this->userId ?? 0;
        return $this->cachedSupervisors->reject(function ($supervisor) use ($currentUserId) {
            return $supervisor && isset($supervisor->id) && $supervisor->id === $currentUserId;
        });
    }

    /**
     * Schließt das Modal und bereinigt alle Daten
     */
    public function closeCreateEmployeeModal(): void
    {
        $this->resetErrorBag();
        $this->modal('create-employee')->close();

        $this->reset([
            'gender', 'name', 'last_name', 'email', 'selectedTeams',
            'department', 'supervisor', 'selectedRoles', 'profession',
            'stage', 'joined_at', 'employee_status', 'model_status',
            'invitations',
        ]);

        // Private Cache-Properties zurücksetzen
        $this->cachedTeams = null;
        $this->cachedDepartments = null;
        $this->cachedRoles = null;
        $this->cachedProfessions = null;
        $this->cachedStages = null;
        $this->cachedSupervisors = null;

        $this->showCreateModal = false;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.create');
    }
}
