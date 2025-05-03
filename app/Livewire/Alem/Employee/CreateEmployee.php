<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
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
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy(isolate: false)]
class CreateEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee;

    // Modal-Status
    public bool $showCreateModal = false;
    private bool $dataLoaded = false;

    public ?int $userId = null;
    public array $selectedRoles = [];
    public $model_status;
    public $employee_status;
    public $invitations = true;

    // Eigenschaften für vorgeladene Daten - werden von Livewire automatisch befüllt
    public ?int $authUserId = null;
    public ?int $currentTeamId = null;
    public ?int $companyId = null;

    // Benutzer-Felder
    public $gender;
    public $name;
    public $last_name;
    public $email;
    public $email_verified_at;
    public $password;
    public ?Carbon $joined_at = null;
    public $department = null;
    public array $selectedTeams = [];

    // Mitarbeiter-Felder
    public $profession;
    public $stage;
    public $supervisor = null;

    // Cache-Eigenschaften - privat und initialisieren bei Bedarf
    private ?Collection $teams = null;
    private ?Collection $departments = null;
    private ?Collection $roles = null;
    private ?Collection $professions = null;
    private ?Collection $stages = null;
    private ?Collection $supervisors = null;

    /**
     * Lebenszyklusmethode: Wird aufgerufen, wenn das Modal geöffnet wird
     * Initialisiert Standardwerte und lädt Dropdown-Daten
     */
    #[On('create-employee-modal')]
    public function openCreateEmployeeModal(): void
    {
        $this->gender = Gender::Male->value;
        $this->model_status = ModelStatus::ACTIVE->value;
        $this->employee_status = EmployeeStatus::PROBATION->value;
        $this->invitations = true;

        $this->loadRelationForDropDowns();

        $this->showCreateModal = true;
        $this->dataLoaded = false;
    }

    /**
     * Lädt alle erforderlichen Daten für Dropdowns aus dem Cache
     */
    private function loadRelationForDropDowns(): void
    {
        if (!$this->showCreateModal || $this->dataLoaded) {
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

            $this->dataLoaded = true;

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('An error occurred while loading the Relation Data.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }

    /**
     * Lebenszyklusmethode um sicherzustellen, dass Daten auch nach Validierungsfehlern geladen sind
     */
    public function hydrate(): void
    {
        if ($this->showCreateModal && !$this->dataLoaded) {
            $this->loadRelationForDropDowns();
        }
    }

    /**
     * Aktualisiert die Cache-Daten für Professionen und setzt die neue Profession als ausgewählt
     */
    #[On(['profession-created', 'profession-updated', 'profession-deleted'])]
    public function refreshProfessions($id = null): void
    {
        Profession::flushCompanyCache($this->companyId);

        $this->dataLoaded = false;

        $this->professions = null;

        $this->loadRelationForDropDowns();

        if ($id) {
            $this->profession = $id;
        }

        if ($this->profession) {
            $professionExists = $this->professions?->contains('id', $this->profession);
            if (!$professionExists) {
                $this->profession = null;
            }
        }
    }


    /**
     * Gibt die Liste der Berufe zurück
     */
    #[Computed]
    public function professions(): Collection
    {
        if ($this->professions === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->professions ?? collect();
    }

    /**
     * Aktualisiert die Cache-Daten für Stages
     */
    #[On(['stage-created', 'stage-updated', 'stage-deleted'])]
    public function refreshStages($id = null): void
    {
        Stage::flushCompanyCache($this->companyId);

        $this->dataLoaded = false;

        $this->stages = null;

        $this->loadRelationForDropDowns();

        if ($id) {
            $this->stage = $id;
        }

        if ($this->stage) {
            $stageExists = $this->stages?->contains('id', $this->stage);
            if (!$stageExists) {
                $this->stage = null;
            }
        }
    }

    /**
     * Gibt die Liste der Karrierestufen zurück
     */
    #[Computed]
    public function stages(): Collection
    {
        if ($this->stages === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->stages ?? collect();
    }

    /**
     * Aktualisiert die Cache-Daten für Departments
     */
    #[On(['department-updated', 'department-created', 'department-deleted'])]
    public function refreshDepartments($id = null): void
    {
        Department::flushTeamCache($this->currentTeamId);

        $this->dataLoaded = false;

        $this->departments = null;

        $this->loadRelationForDropDowns();

        if ($id) {
            $this->department = $id;
        }

        if ($this->department) {
            $departmentExists = $this->departments?->contains('id', $this->department);
            if (!$departmentExists) {
                $this->department = null;
            }
        }
    }

    /**
     * Gibt die Liste der Abteilungen zurück,
     */
    #[Computed]
    public function departments(): Collection
    {
        if ($this->departments === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->departments ?? collect();
    }


    /**
     * Gibt die Liste der Rollen zurück
     */
    #[Computed]
    public function roles(): Collection
    {
        if ($this->roles === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->roles ?? collect();
    }

    /**
     * Gibt die Liste der Teams zurück
     */
    #[Computed]
    public function teams(): Collection
    {
        if ($this->teams === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->teams ?? collect();
    }

    /**
     * Gibt die Liste der Supervisoren zurück
     */
    #[Computed]
    public function supervisors(): Collection
    {
        if ($this->supervisors === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->supervisors ?? collect();
    }

    /**
     * Gibt die Optionen für den Mitarbeiterstatus zurück
     */
    #[Computed]
    public function employeeStatusOptions(): array
    {
        return EmployeeStatus::getEmployeeOptions();
    }

    /**
     * Gibt die Optionen für den Modelstatus zurück
     */
    #[Computed]
    public function modelStatusOptions(): array
    {
        return ModelStatus::getModelOptions();
    }

    /**
     * Führt alle notwendigen DB-Operationen in einer Transaktion aus und speichert den Mitarbeiter
     */
    public function saveEmployee(): void
    {
        if (!$this->dataLoaded) {
            $this->loadRelationForDropDowns();
        }

        $this->password = Str::password();

        $this->showCreateModal = false;

        $this->validate();

        try {
            DB::beginTransaction();

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

            if (!empty($this->selectedRoles)) {
                $user->roles()->sync($this->selectedRoles);
            }

            Employee::create([
                'user_id' => $user->id,
                'uuid' => (string)Str::uuid(),
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

                if (!empty($teamsWithRole)) {
                    $user->teams()->attach($teamsWithRole);
                }
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

            Flux::toast(
                text: __('An error occurred while saving the employee.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }

    /**
     * Setzt das Formular zurück und schließt das Modal
     */
    public function closeCreateEmployeeModal(): void
    {
        $this->reset([
            'gender', 'name', 'last_name', 'email', 'selectedTeams',
            'department', 'supervisor', 'selectedRoles', 'profession',
            'stage', 'joined_at', 'employee_status', 'model_status',
            'invitations', 'password'
        ]);

        $this->resetErrorBag();

        $this->modal('create-employee')->close();

        // HINZUGEFÜGT: Cache-Properties bereinigen, um Speicher freizugeben
        $this->teams = null;
        $this->departments = null;
        $this->roles = null;
        $this->professions = null;
        $this->stages = null;
        $this->supervisors = null;

        $this->dataLoaded = false;
        $this->showCreateModal = false;
    }

    public function render(): View
    {
        if ($this->showCreateModal && !$this->dataLoaded) {
            $this->loadRelationForDropDowns();
        }

        return view('livewire.alem.employee.create', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }
}
