<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Livewire\Alem\Employee\Helper\WithDropDownsCollections;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateEmployee extends Component
{
    use AuthorizesRequests;
    use WithDropDownsCollections, ValidateEmployee, HandleCatchError;
    use ModelStatusOptions, EmployeeStatusOptions, GenderOptions;

    /**
     * SICHERHEIT: Locked Properties können nicht von außen manipuliert werden
     * @var int|null
     *  ID des authentifizierten Benutzers. Wird von der übergeordneten View übergeben.
     */
    #[Locked]
    public ?int $authUserId = null;

    #[Locked]
    public ?int $currentTeamId = null;

    #[Locked]
    public ?int $companyId = null;

    /** Modal-Status */
    public bool $showCreateModal = false;

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
     * Arrays statt Collections für bessere Performance
     */
    public array $teams = [];
    public array $departments = [];
    public array $roles = [];
    public array $professions = [];
    public array $stages = [];
    public array $supervisors = [];


    #[On('create-employee-modal')]
    public function openCreateEmployeeModal(): void
    {
        $this->resetFormData();

        // Setze Standardwerte
        $this->gender = Gender::Male;
        $this->selectedTeams = $this->currentTeamId ? [$this->currentTeamId] : [];
        $this->model_status = ModelStatus::ACTIVE;
        $this->employee_status = EmployeeStatus::PROBATION;
        $this->invitation = true;
        $this->showCreateModal = true;

        // Lade ALLE Collections EINMALIG beim Öffnen
        $this->loadAllCollections();
    }

    /**
     * Lädt alle benötigten Daten als Arrays
     */
    private function loadAllCollections(): void
    {
        if (!$this->companyId) {
            return;
        }

        try {
            $this->teams = Team::getCompanyTeams($this->companyId)
                ->map(fn($team) => [
                    'id' => $team->id,
                    'name' => $team->name
                ])
                ->toArray();

            $this->departments = Department::getDepartmentsForTeam($this->currentTeamId)
                ->map(fn($dept) => [
                    'id' => $dept->id,
                    'name' => $dept->name
                ])
                ->toArray();

            $this->roles = Role::getEmployeePanelRoles($this->companyId)
                ->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'is_manager' => $role->is_manager ?? false
                ])
                ->toArray();

            $this->professions = Profession::getCompanyProfessions($this->companyId)
                ->map(fn($prof) => [
                    'id' => $prof->id,
                    'name' => $prof->name
                ])
                ->toArray();

            $this->stages = Stage::getCompanyStages($this->companyId)
                ->map(fn($stage) => [
                    'id' => $stage->id,
                    'name' => $stage->name
                ])
                ->toArray();

            $this->supervisors = $this->loadSupervisors();

        } catch (\Exception $e) {
            // Collections bleiben leer bei Fehler
        }
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

    #[On(['profession-created', 'profession-updated', 'profession-deleted'])]
    public function refreshProfessions(?int $id = null): void
    {
        $this->refreshCollectionData('professions', $id, [
            'companyId' => true
        ]);
    }

    #[On(['stage-created', 'stage-updated', 'stage-deleted'])]
    public function refreshStages(?int $id = null): void
    {
        $this->refreshCollectionData('stages', $id, [
            'companyId' => true
        ]);
    }

    #[On(['department-updated', 'department-created', 'department-deleted'])]
    public function refreshDepartments(?int $id = null): void
    {
        $this->refreshCollectionData('departments', $id, [
            'currentTeamId' => true
        ]);
    }

    /**
     * Lädt Supervisors als Array
     */
    private function loadSupervisors(): array
    {
        $supervisors = User::getCompanyManagers($this->companyId);
        $currentUserId = $this->userId ?? 0;

        return $supervisors
            ->reject(fn($sup) => $sup->id === $currentUserId)
            ->map(fn($sup) => [
                'id' => $sup->id,
                'name' => $sup->name,
                'last_name' => $sup->last_name,
                'full_name' => $sup->name . ' ' . $sup->last_name,
                'profile_photo_path' => $sup->profile_photo_path
            ])
            ->toArray();
    }
    /**
     * Schließt das Modal und bereinigt alle Daten
     */
    public function closeCreateEmployeeModal(): void
    {
        $this->modal('create-employee')->close();
        $this->resetFormData();

        // Setzt verzögert 1ms die Formularfelder zurück
        $this->js("
        setTimeout(() => {
            \$wire.resetFormData();
        }, 1);
    ");

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
            'teams', 'departments', 'roles',
            'professions', 'stages', 'supervisors'
        ]);
    }

    public function render(): View
    {
        return view('livewire.alem.employee.create');
    }
}
