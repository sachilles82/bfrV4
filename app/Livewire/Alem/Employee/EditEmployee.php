<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Employee;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\User;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Spatie\Role;
use App\Models\Team;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use Illuminate\Support\Collection;

#[Lazy(isolate: false)]
class EditEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee;

    // Modal state
    public bool $showEditModal = false;
    private bool $dataLoaded = false;

    // User identification
    #[Locked]
    public ?int $userId = null;
    public ?User $user = null;

    // Eigenschaften für vorgeladene Daten - diese werden automatisch von Livewire befüllt
    public ?int $authUserId = null;
    public ?int $currentTeamId = null;
    public ?int $companyId = null;

    // User form fields
    public ?string $name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $gender = null;
    public ?ModelStatus $model_status = null;
    public $joined_at = null;
    public $department = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    // Employee fields
    public ?EmployeeStatus $employee_status = null;
    public $profession = null;
    public $stage = null;
    public $supervisor = null;

    // Optimierung: Cache-Eigenschaften privat lassen und initialisieren
    private ?Collection $teams = null;
    private ?Collection $departments = null;
    private ?Collection $roles = null;
    private ?Collection $professions = null;
    private ?Collection $stages = null;
    private ?Collection $supervisors = null;

    #[On('edit-employee-modal')]
    public function openEditEmployeeModal($userId): void
    {

        // $this->authorize('update', User::class);

        $this->userId = $userId;

        $this->loadRelationForDropDowns();

        // Kein Join in der Edit und Create verwenden. Nur in der Table ist es sinnvoll
        $this->user = User::with([
            'employee:id,user_id,employee_status,profession_id,stage_id,supervisor_id',
            'teams:id,name',
            'roles:id,name,is_manager',
            'department:id,name'
        ])->findOrFail($this->userId);

        $this->loadEmployeeData();

        $this->showEditModal = true;
        $this->dataLoaded = false;

    }

    /**
     * Fill form with user data
     */
    protected function loadEmployeeData(): void
    {
        if (!$this->user) return;

        $this->gender = $this->user->gender;
        $this->name = $this->user->name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;

        $this->selectedTeams = $this->user->teams->pluck('id')->toArray();
        $this->department = $this->user->department_id;

        // ModelStatus ENUM
        $this->model_status = $this->user->model_status;
        $this->joined_at = $this->user->joined_at;


        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();

        if ($employee = $this->user->employee) {
            // Employee Status ENUM
            $this->employee_status = $employee->employee_status;

            $this->profession = $employee->profession_id;
            $this->stage = $employee->stage_id;
            $this->supervisor = $employee->supervisor_id;
        }
    }


    protected function loadRelationForDropDowns(): void
    {
        if (!$this->showEditModal || $this->dataLoaded) {
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
            \Illuminate\Support\Facades\Log::error("Fehler beim Laden der Relationen: " . $e->getMessage());
            Flux::toast(
                text: __('An error occurred while loading the Relation Data.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }


    /**
     * Update employee in database
     */
    public function updateEmployee(): void
    {
        $this->validate();

        try {

        DB::beginTransaction();

        User::where('id', $this->userId)->update([
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'gender' => $this->gender,
            'model_status' => $this->model_status,
            'joined_at' => $this->joined_at ? Carbon::parse($this->joined_at)->format('Y-m-d') : null,
            'department_id' => $this->department,
        ]);

        Employee::updateOrCreate(
            ['user_id' => $this->userId],
            [
                'employee_status' => $this->employee_status,
                'profession_id' => $this->profession,
                'stage_id' => $this->stage,
                'supervisor_id' => $this->supervisor,
            ]
        );

        $this->user->loadMissing('roles:id,name,is_manager');

        if ($this->user->relationLoaded('roles')) {
            $this->checkRoleChangesForManager($this->user);
        }

        $this->user->roles()->sync($this->selectedRoles);
        $this->user->teams()->sync($this->selectedTeams);

        DB::commit();

        $this->closeEditEmployeeModal();

        $this->dispatch('employee-updated');

        Flux::toast(
            text: __('Employee Profile updated successfully.'),
            heading: __('Success.'),
            variant: 'success'
        );

        } catch (\Throwable $e) {

            Flux::toast(
                text: __('An error occurred while editing the employee.'),
                heading: __('Error.'),
                variant: 'danger'
            );
        }
    }

    /**
     * Setzt das Formular zurück und schließt das Modal.
     * Bereinigt zusätzlich alle Cache-Properties, um Speicher freizugeben.
     */
    public function closeEditEmployeeModal(): void
    {
        // Formularfelder zurücksetzen
        $this->reset([
            'name', 'last_name', 'email', 'gender', 'model_status', 'joined_at', 'department',
            'employee_status', 'profession', 'stage', 'supervisor',
            'selectedRoles', 'selectedTeams',
        ]);

        // Fehlermeldungen zurücksetzen
        $this->resetErrorBag();

        // Modal schließen
        $this->modal('edit-employee')->close();

        // HINZUGEFÜGT: Cache-Properties bereinigen, um Speicher freizugeben
        $this->teams = null;
        $this->departments = null;
        $this->roles = null;
        $this->professions = null;
        $this->stages = null;
        $this->supervisors = null;

        // Status zurücksetzen
        $this->dataLoaded = false;
        $this->showEditModal = false;
    }

    /**
     * Lebenszyklusmethode um sicherzustellen, dass Daten auch nach Validierungsfehlern geladen sind
     */
    public function hydrate(): void
    {
        if ($this->showEditModal && !$this->dataLoaded) {
            $this->loadRelationForDropDowns();
        }
    }

    /**
     * Aktualisiert die Cache-Daten für Professionen und setzt die neue Profession als ausgewählt.
     * Wird aufgerufen, wenn Professionen erstellt, aktualisiert oder gelöscht werden.
     *
     * @param int|null $id Die ID der neuen/aktualisierten Profession, falls vorhanden
     * @return void
     */
    #[On(['profession-created', 'profession-updated', 'profession-deleted'])]
    public function refreshProfessions(?int $id = null): void
    {
        // Cache in der Datenbank leeren
        Profession::flushCompanyCache($this->companyId);

        // Lokale Cache-Variable zurücksetzen
        $this->professions = null;

        // Laden-Status zurücksetzen
        $this->dataLoaded = false;

        // Daten neu laden
        $this->loadRelationForDropDowns();

        // Falls eine neue Profession erstellt wurde, diese automatisch auswählen
        if ($id) {
            $this->profession = $id;
        }

        // Prüfe, ob die aktuell ausgewählte Profession noch existiert
        if ($this->profession) {
            // Null-Safety-Check mit dem Optional-Chaining-Operator (?->)
            $professionExists = $this->professions?->contains('id', $this->profession);
            if (!$professionExists) {
                $this->profession = null;
            }
        }
    }


    /**
     * Gibt die Liste der Berufe (Professionen) zurück.
     * Enthält zusätzliche Null-Safety-Checks.
     *
     * @return Collection
     */
    #[Computed]
    public function professions(): Collection
    {
        // Prüfe, ob Daten geladen werden müssen
        if ($this->professions === null && $this->showEditModal) {
            $this->loadRelationForDropDowns();
        }

        // Null-Safety-Check nach dem Laden
        return $this->professions ?? collect();
    }

    /**
     * Aktualisiert die Cache-Daten für Stages.
     * Wird aufgerufen, wenn Stages erstellt, aktualisiert oder gelöscht werden.
     *
     * @param int|null $id Die ID der neuen/aktualisierten Stage, falls vorhanden
     * @return void
     */
    #[On(['stage-created', 'stage-updated', 'stage-deleted'])]
    public function refreshStages(?int $id = null): void
    {
        // Cache in der Datenbank leeren
        Stage::flushCompanyCache($this->companyId);

        // Lokale Cache-Variable zurücksetzen
        $this->stages = null;

        // Laden-Status zurücksetzen
        $this->dataLoaded = false;

        // Daten neu laden
        $this->loadRelationForDropDowns();

        // Falls eine neue Stage erstellt wurde, diese automatisch auswählen
        if ($id) {
            $this->stage = $id;
        }

        // Prüfe, ob die aktuell ausgewählte Stage noch existiert
        if ($this->stage) {
            // Null-Safety-Check mit dem Optional-Chaining-Operator (?->)
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
        if ($this->stages === null && $this->showEditModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->stages ?? collect();
    }

    /**
     * Aktualisiert die Cache-Daten für Departments.
     * Wird aufgerufen, wenn Departments erstellt, aktualisiert oder gelöscht werden.
     *
     * @param int|null $id Die ID des neuen/aktualisierten Departments, falls vorhanden
     * @return void
     */
    #[On(['department-updated', 'department-created', 'department-deleted'])]
    public function refreshDepartments(?int $id = null): void
    {
        // Cache in der Datenbank leeren
        Department::flushTeamCache($this->currentTeamId);

        // Lokale Cache-Variable zurücksetzen
        $this->departments = null;

        // Laden-Status zurücksetzen
        $this->dataLoaded = false;

        // Daten neu laden
        $this->loadRelationForDropDowns();

        // Falls ein neues Department erstellt wurde, dieses automatisch auswählen
        if ($id) {
            $this->department = $id;
        }

        // Prüfe, ob das aktuell ausgewählte Department noch existiert
        if ($this->department) {
            // Null-Safety-Check mit dem Optional-Chaining-Operator (?->)
            $departmentExists = $this->departments?->contains('id', $this->department);
            if (!$departmentExists) {
                $this->department = null;
            }
        }
    }

    /**
     * Gibt die Liste der Abteilungen (Departments) zurück.
     * Enthält Null-Safety-Checks und lädt Daten bei Bedarf nach.
     *
     * @return Collection
     */
    #[Computed]
    public function departments(): Collection
    {
        // Prüfe, ob Daten geladen werden müssen
        if ($this->departments === null && $this->showEditModal) {
            $this->loadRelationForDropDowns();
        }

        // Null-Safety-Check nach dem Laden
        return $this->departments ?? collect();
    }


    /**
     * Gibt die Liste der Rollen zurück
     */
    #[Computed]
    public function roles(): Collection
    {
        if ($this->roles === null && $this->showEditModal) {
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
        if ($this->teams === null && $this->showEditModal) {
            $this->loadRelationForDropDowns();
        }
        return $this->teams ?? collect();
    }

    /**
     * Prüft auf Änderungen bei Manager-Rollen und leert ggf. den Manager-Cache.
     * Geht davon aus, dass $user->roles und $this->roles geladen sind.
     *
     * @param User $user Der Benutzer (mit geladenen Rollen).
     * @return void
     */
    private function checkRoleChangesForManager(User $user): void
    {
        // Sicherstellen, dass benötigte Daten vorhanden sind
        if (!$user->relationLoaded('roles')) {
            // Versuche nachzuladen, wenn die Relation fehlt (Fallback)
            $user->loadMissing('roles:id,is_manager');
            if (!$user->relationLoaded('roles')) {
                // Abbruch, wenn Rollen nicht geladen werden konnten
                return;
            }
        }

        if (empty($this->companyId)) {
            // Abbruch, wenn keine Firmen-ID vorhanden ist
            return;
        }

        // Hole die verfügbaren Rollen (aus dem lokalen Property-Cache)
        $availableRoles = $this->roles;
        if ($availableRoles === null) {
            // Versuche Dropdown-Daten neu zu laden, wenn lokaler Cache leer ist
            $this->loadRelationForDropDowns();
            $availableRoles = $this->roles;
            if ($availableRoles === null) {
                // Abbruch, wenn verfügbare Rollen nicht geladen werden konnten
                return;
            }
        }

        // Alte Manager-Rollen-IDs aus der geladenen User-Relation extrahieren
        $oldManagerRoleIds = $user->roles
            ->where('is_manager', true)
            ->pluck('id')
            ->sort()->values()->all();

        // Neue Manager-Rollen-IDs aus der aktuellen Auswahl ($this->selectedRoles) bestimmen
        $newManagerRoleIds = $availableRoles
            ->whereIn('id', $this->selectedRoles)
            ->where('is_manager', true)
            ->pluck('id')
            ->sort()->values()->all();

        // Vergleiche alte und neue Manager-Rollen
        if ($oldManagerRoleIds !== $newManagerRoleIds) {
            // Leere den globalen Manager-Cache für die Firma
            User::flushManagerCache($this->companyId);
            // Setze lokale Caches zurück, um Neuladen zu erzwingen
            $this->supervisors = null;
            $this->dataLoaded = false;
        }
    }

    /**
     * Gibt die Liste der Supervisoren zurück, exklusive des aktuell bearbeiteten Benutzers.
     * Enthält zusätzliche Null-Safety-Checks.
     *
     * @return Collection
     */
    #[Computed]
    public function supervisors(): Collection
    {
        // Prüfe, ob Daten geladen werden müssen
        if ($this->supervisors === null && $this->showEditModal) {
            $this->loadRelationForDropDowns();
        }

        // Zusätzlicher Null-Safety-Check nach dem Laden
        if ($this->supervisors === null) {
            return collect();
        }

        // Zusätzlicher Null-Safety-Check für userId
        $currentUserId = $this->userId ?? 0;

        // Filtere den aktuellen Benutzer aus der Liste, prüfe, ob supervisor ein gültiges Objekt mit id ist
        return $this->supervisors->reject(function ($supervisor) use ($currentUserId) {

            return $supervisor && isset($supervisor->id) && $supervisor->id === $currentUserId;
        });
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

    public function render(): View
    {
        return view('livewire.alem.employee.edit');
    }
}
