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
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

//#[Lazy(isolate: false)]
class CreateEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee;
    use ModelStatusOptions, EmployeeStatusOptions, GenderOptions;

    /** Modal-Status */
    public bool $showCreateModal = false;
    private bool $dataLoaded = false;

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
    // Einladungs-Einstellungen **Ändere es in invitation ohne s
    public bool $invitations = false;

    /**
     * Cache-Eigenschaften - Verwenden jetzt die neuen generischen Cache-Methoden
     * Diese werden bei Bedarf geladen und automatisch invalidiert
     */
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
        $this->gender = Gender::Male;
        $this->model_status = ModelStatus::ACTIVE;
        $this->employee_status = EmployeeStatus::PROBATION;
        $this->invitations = true;

        $this->loadRelationForDropDowns();

        $this->showCreateModal = true;
        $this->dataLoaded = false;
    }

    /**
     * Lädt alle erforderlichen Daten für Dropdowns aus dem Cache
     *
     * Nutzt jetzt die neuen generischen Cache-Methoden aus dem WithRedisCache Trait
     */
    private function loadRelationForDropDowns(): void
    {
        if (!$this->showCreateModal || $this->dataLoaded) {
            return;
        }

//        try {
            $companyId = $this->companyId;
            $teamId = $this->currentTeamId;

            /** Teams laden mit neuer generischer Cache-Methode */
            if ($this->teams === null) {
                $this->teams = Team::getCompanyTeams($companyId);
            }

            /** Departments laden mit neuer generischer Cache-Methode */
            if ($this->departments === null) {
                $this->departments = Department::getDepartmentsForTeam($teamId);
            }

            /** Supervisors laden mit bestehender Cache-Methode */
            if ($this->supervisors === null) {
                $this->supervisors = User::getCompanyManagers($companyId);
            }

            /** Roles laden mit bestehender Cache-Methode */
            if ($this->roles === null) {
                $this->roles = Role::getEmployeePanelRoles($companyId);
            }

            /**
             * Professions laden mit neuer generischer Cache-Methode
             * Nutzt automatisch dreistufigen Cache (Request->Redis->DB)
             */
            if ($this->professions === null) {
                $this->professions = Profession::getCompanyProfessions($companyId);
            }

            /**
             * Stages laden mit neuer generischer Cache-Methode
             * Nutzt automatisch dreistufigen Cache (Request->Redis->DB)
             */
            if ($this->stages === null) {
                $this->stages = Stage::getCompanyStages($companyId);
            }

            $this->dataLoaded = true;

//        }
//        catch (\Throwable $e) {
//
//            Flux::toast(
//                text: __('An error occurred while loading the Relation Data.'),
//                heading: __('Error.'),
//                variant: 'danger'
//            );
//        }
    }

    /**
     * Führt alle notwendigen DB-Operationen in einer Transaktion aus und speichert den Mitarbeiter
     */
    public function saveEmployee(): void
    {
        if (!$this->dataLoaded) {
            $this->loadRelationForDropDowns();
        }

        $generatedPassword  = Str::password();
        $this->validate();

        try {
            DB::beginTransaction();

            $user = User::create([
                'gender' => $this->gender,
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($generatedPassword ),
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

            DB::rollBack();
            Log::error("Fehler beim Erstellen des Mitarbeiters: " . $e->getMessage(), [
                'exception' => $e,
                'acting_user_id' => $this->authUserId ?? auth()->id(),
                'formData' => collect($this->only([
                    'gender',
                    'name',
                    'last_name',
                    'email',
                    'model_status',
                    'joined_at',
                    'department',
                    'selectedTeams',
                    'selectedRoles',
                    'employee_status',
                    'profession',
                    'stage',
                    'supervisor',
                    'invitations'
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
     * Event-Handler: Aktualisiert Professions-Cache automatisch
     */
    #[On(['profession-created', 'profession-updated', 'profession-deleted'])]
    public function refreshProfessions(?int $id = null): void
    {
        /** Manuell den Company-Cache leeren für absolute Sicherheit */
        if ($this->companyId) {
            Profession::flushCompanyCache($this->companyId);
        }

        /** Lokale Cache-Variable zurücksetzen */
        $this->professions = null;

        /** Wichtig: dataLoaded auf false setzen */
        $this->dataLoaded = false;

        /** Daten neu laden - dies holt die aktuellen Daten aus der DB */
        $this->loadRelationForDropDowns();

        /** Falls eine neue Profession erstellt wurde, diese automatisch auswählen */
        if ($id) {
            $this->profession = $id;
        }

        /** Prüfe, ob die aktuell ausgewählte Profession noch existiert */
        if ($this->profession && $this->professions) {
            $professionExists = $this->professions->contains('id', $this->profession);
            if (!$professionExists) {
                $this->profession = null;
            }
        }
    }

    #[On(['stage-created', 'stage-updated', 'stage-deleted'])]
    public function refreshStages(?int $id = null): void
    {
        /** Manuell den Company-Cache leeren für absolute Sicherheit */
        if ($this->companyId) {
            Stage::flushCompanyCache($this->companyId);
        }

        /** Lokale Cache-Variable zurücksetzen */
        $this->stages = null;

        /** Wichtig: dataLoaded auf false setzen */
        $this->dataLoaded = false;

        /** Daten neu laden - dies holt die aktuellen Daten aus der DB */
        $this->loadRelationForDropDowns();

        /** Falls eine neue Stage erstellt wurde, diese automatisch auswählen */
        $this->loadRelationForDropDowns();

        /** Falls eine neue Stage erstellt wurde, diese automatisch auswählen */
        if ($id) {
            $this->stage = $id;

            /** Prüfe, ob die aktuell ausgewählte Stage noch existiert */
            if ($this->stages && !$this->stages->contains('id', $id)) {
                // Falls nicht vorhanden, nochmals Cache leeren und neu laden
                Stage::flushCompanyCache($this->companyId);
                $this->stages = null;
                $this->loadRelationForDropDowns();
            }
        }

        /** Prüfe, ob die aktuell ausgewählte Stage noch existiert */
        if ($this->stage && $this->stages) {
            $stageExists = $this->stages->contains('id', $this->stage);
            if (!$stageExists) {
                $this->stage = null;
            }
        }
    }

    /**
     * Event-Handler: Aktualisiert Departments-Cache automatisch
     */
    #[On(['department-updated', 'department-created', 'department-deleted'])]
    public function refreshDepartments(?int $id = null): void
    {
        /** Cache wird automatisch durch das Trait geleert */
        $this->departments = null;
        $this->dataLoaded = false;

        /** Daten neu laden */
        $this->loadRelationForDropDowns();

        /** Falls ein neues Department erstellt wurde, dieses automatisch auswählen */
        if ($id) {
            $this->department = $id;
        }

        /** Prüfe, ob das aktuell ausgewählte Department noch existiert */
        if ($this->department) {
            $departmentExists = $this->departments?->contains('id', $this->department);
            if (!$departmentExists) {
                $this->department = null;
            }
        }
    }

    /**
     * Computed Properties mit Null-Safety
     */
    #[Computed]
    public function professions(): Collection
    {
        if ($this->professions === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->professions ?? collect();
    }

    #[Computed]
    public function stages(): Collection
    {
        if ($this->stages === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->stages ?? collect();
    }

    #[Computed]
    public function departments(): Collection
    {
        if ($this->departments === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }

        // Null-Safety-Check nach dem Laden
        return $this->departments ?? collect();
    }

    #[Computed]
    public function roles(): Collection
    {
        if ($this->roles === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->roles ?? collect();
    }

    #[Computed]
    public function teams(): Collection
    {
        if ($this->teams === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->teams ?? collect();
    }

    #[Computed]
    public function supervisors(): Collection
    {
        if ($this->supervisors === null && $this->showCreateModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->supervisors ?? collect();
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

        /** Cache-Properties bereinigen */
        $this->teams = null;
        $this->departments = null;
        $this->roles = null;
        $this->professions = null;
        $this->stages = null;
        $this->supervisors = null;

        $this->dataLoaded = false;
        $this->showCreateModal = false;
    }

    /**
     * Hydrate-Hook um sicherzustellen, dass Daten auch nach Validierungsfehlern geladen sind
     */
    public function hydrate(): void
    {
        if ($this->showCreateModal && !$this->dataLoaded) {
            $this->loadRelationForDropDowns();
        }
    }

    public function render(): View
    {
        return view('livewire.alem.employee.create');
    }

    /**
     * Lädt alle Dropdown-Daten neu
     * Pragmatische Lösung für Cache-Refresh
     */
    public function refreshDropdownData(): void
    {
        // Leere alle relevanten Caches
        if ($this->companyId) {
            Profession::flushCompanyCache($this->companyId);
            Stage::flushCompanyCache($this->companyId);
            Department::flushTeamCache($this->currentTeamId);
            User::flushManagerCache($this->companyId);
            Role::flushCompanyCache($this->companyId);
        }

        // Setze alle lokalen Cache-Variablen zurück
        $this->professions = null;
        $this->stages = null;
        $this->departments = null;
        $this->supervisors = null;
        $this->roles = null;
        $this->teams = null;

        // Lade-Status zurücksetzen
        $this->dataLoaded = false;

        // Daten neu laden
        $this->loadRelationForDropDowns();

        // Optional: Erfolgsmeldung
        Flux::toast(
            text: __('Dropdown data refreshed successfully.'),
            heading: __('Success'),
            variant: 'success'
        );
    }
}
