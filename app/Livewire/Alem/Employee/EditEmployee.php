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
            'roles:id,name',
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

//        try {

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

        $employee = Employee::where('user_id', $this->userId)->first();

        $employee->update([
            'employee_status' => $this->employee_status,
            'profession_id' => $this->profession,
            'stage_id' => $this->stage,
            'supervisor_id' => $this->supervisor,
        ]);

        $updatedUser = User::find($this->userId);

        $this->checkRoleChangesForManager();

        $updatedUser->roles()->sync($this->selectedRoles);
        $updatedUser->teams()->sync($this->selectedTeams);

        DB::commit();

        $this->closeEditEmployeeModal();

        $this->dispatch('employee-updated');

        Flux::toast(
            text: __('Employee Profile updated successfully.'),
            heading: __('Success.'),
            variant: 'success'
        );

//        } catch (\Throwable $e) {
//
//            Flux::toast(
//                text: __('An error occurred while editing the employee.'),
//                heading: __('Error.'),
//                variant: 'danger'
//            );
//        }
    }

    /**
     * Setzt das Formular zurück und schließt das Modal
     */
    public function closeEditEmployeeModal(): void
    {
        $this->reset([
            'name', 'last_name', 'email', 'gender', 'model_status', 'joined_at', 'department',
            'employee_status', 'profession', 'stage', 'supervisor',
            'selectedRoles', 'selectedTeams',
        ]);

        $this->resetErrorBag();

        $this->modal('edit-employee')->close();

        $this->dataLoaded = false;
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
        if ($this->professions === null && $this->showEditModal) {
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
        if ($this->stages === null && $this->showEditModal) {
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
        if ($this->departments === null && $this->showEditModal) {
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
     * Überprüft, ob sich das Set der Manager-Rollen eines Benutzers ändert
     * und aktualisiert die Supervisor-Liste entsprechend. (Robuste Version mit Detail-Log)
     *
     * @return void
     */
    private function checkRoleChangesForManager(): void
    {
        $user = User::with('roles:id,is_manager')->find($this->userId);

        $oldManagerRoleIds = $user->roles
            ->where('is_manager', true)
            ->pluck('id')
            ->sort()->values()->all();

        $availableRoles = $this->roles;
        if ($availableRoles === null) {
            Log::warning("Roles collection was null in checkRoleChangesForManager for user {$this->userId}. Attempting to load.");

            $this->loadRelationForDropDowns();
            $availableRoles = $this->roles;
            if ($availableRoles === null) {
                Log::error("Failed to load roles collection in checkRoleChangesForManager for user {$this->userId}. Cannot compare roles.");
                return;
            }
        }

        $newManagerRoleIds = $availableRoles
            ->whereIn('id', $this->selectedRoles)
            ->where('is_manager', true)
            ->pluck('id')
            ->sort()->values()->all();

        if ($oldManagerRoleIds !== $newManagerRoleIds) {

            User::flushManagerCache($this->companyId);

            $this->dataLoaded = false;
            $this->supervisors = null;
        }
    }

    /**
     * Gibt die Liste der Supervisoren zurück,
     * EXKLUSIVE des aktuell bearbeiteten Benutzers.
     * Fügt Debugging-Logs hinzu.
     *
     * @return \Illuminate\Support\Collection
     */
    #[Computed]
    public function supervisors(): Collection
    {
        // Logge den Aufruf und die ID, die ausgeschlossen werden soll
        Log::debug("Computed property 'supervisors()' aufgerufen.");
        Log::debug("-> User ID (this->userId) zum Ausschließen: " . ($this->userId ?? 'NULL'));

        // 1. Sicherstellen, dass die Supervisor-Daten grundsätzlich geladen sind
        if ($this->supervisors === null && $this->showEditModal) {
            Log::debug("-> Supervisors Collection ist null und Modal ist offen. Rufe loadRelationForDropDowns().");
            $this->loadRelationForDropDowns();
            Log::debug("-> loadRelationForDropDowns() ausgeführt.");
        } elseif ($this->supervisors !== null) {
            Log::debug("-> Supervisors Collection ist bereits geladen (nicht null).");
        } elseif (!$this->showEditModal) {
            Log::debug("-> Modal ist nicht offen (showEditModal=false), keine Daten geladen.");
        }

        // 2. Wenn die Liste nach dem Ladeversuch immer noch null ist (z.B. Fehler),
        //    eine leere Collection zurückgeben.
        if ($this->supervisors === null) {
            Log::warning("-> Supervisors Collection ist immer noch null. Gebe leere Collection zurück.");
            return collect(); // Leere Collection statt null
        }

        // Logge die Daten *vor* dem Filtern
        $initialCount = $this->supervisors->count();
        $initialIds = $this->supervisors->pluck('id')->implode(', ');
        Log::debug("-> Anzahl Supervisoren VOR Filterung: {$initialCount}");
        Log::debug("-> IDs VOR Filterung: [{$initialIds}]");

        // 3. Filtere die geladene Collection: Entferne den aktuellen Benutzer.
        $userIdToExclude = $this->userId; // In lokaler Variable speichern für Closure
        $filteredSupervisors = $this->supervisors->reject(function ($supervisor) use ($userIdToExclude) {
            $shouldReject = $supervisor->id === $userIdToExclude;
            // Optional: Sehr detailliertes Logging für jeden Vergleich (kann viele Logs erzeugen!)
            // Log::debug("---> Vergleiche Supervisor ID {$supervisor->id} mit User ID {$userIdToExclude}. Ergebnis (reject?): " . ($shouldReject ? 'JA' : 'NEIN'));
            return $shouldReject;
        });

        // Logge die Daten *nach* dem Filtern
        $finalCount = $filteredSupervisors->count();
        $finalIds = $filteredSupervisors->pluck('id')->implode(', ');
        Log::debug("-> Anzahl Supervisoren NACH Filterung: {$finalCount}");
        Log::debug("-> IDs NACH Filterung: [{$finalIds}]");

        // 4. Gib die gefilterte Liste zurück
        return $filteredSupervisors;
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
        return view('livewire.alem.employee.edit', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }
}
