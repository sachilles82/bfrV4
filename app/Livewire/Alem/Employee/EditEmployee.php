<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
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
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

//#[Lazy(isolate: false)]
class EditEmployee extends Component
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

    #[Locked]
    public ?int $userId = null;

    /** Modal-Status */
    public bool $showEditModal = false;

    // User identification
    public ?User $user = null;

    /** Benutzer-Felder */
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
    public $profession; // check Profession mit integer
    public $stage;// check Stage mit integer
    public ?int $supervisor = null;

    /**
     * Arrays statt Collections für bessere Performance
     */
    public array $teams = [];
    public array $departments = [];
    public array $roles = [];
    public array $professions = [];
    public array $stages = [];
    public array $supervisors = [];

    #[On('edit-employee-modal')]
    public function openEditEmployeeModal($userId): void
    {
        // $this->authorize('update', User::class);

        $this->userId = $userId;

        // Kein Join in der Edit und Create verwenden. Nur in der Table ist es sinnvoll
        $this->user = User::with([
            'employee:id,user_id,employee_status,profession_id,stage_id,supervisor_id',
            'teams:id,name',
            'roles:id,name,is_manager',
            'department:id,name'
        ])->findOrFail($this->userId);

        $this->loadEmployeeData();

        $this->loadRelationForDropDowns();

        $this->showEditModal = true;

    }

    /**
     * Befülle die Form mit User Employee Daten
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

    /**
     * Lädt alle erforderlichen Daten für Dropdowns aus dem Cache
     */
    protected function loadRelationForDropDowns(): void
    {
//        if (!$this->showEditModal || $this->dataLoaded) {
//            return;
//        }
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
        } catch (\Throwable $e) {
            $this->handleLoadingError($e);
        }
    }

    /**
     * Aktualisiert die Benutzer- und Mitarbeiterdaten in der Datenbank.
     */
    public function updateEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {

                User::where('id', $this->userId)->update([
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'gender' => $this->gender,
                    'model_status' => $this->model_status,
                    'joined_at' => $this->joined_at?->toDateString(),
                    'department_id' => $this->department,
                ]);

                $this->updateEmployeeData();
                $this->syncRelations();

            });

            $this->closeEditEmployeeModal();
            $this->dispatch('employee-updated');

            Flux::toast(
                text: __('Employee Profile updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleEditingError($e);
        }
    }

    /**
     * Erstellt oder aktualisiert die zugehörigen Mitarbeiterdaten.
     */
    private function updateEmployeeData(): void
    {
        Employee::updateOrCreate(
            ['user_id' => $this->userId],
            [
                'employee_status' => $this->employee_status,
                'profession_id' => $this->profession,
                'stage_id' => $this->stage,
                'supervisor_id' => $this->supervisor,
            ]
        );
    }

    /**
     * Synchronisiert die Rollen und Teams des Benutzers.
     */
    private function syncRelations(): void
    {
        // Lade die Rollen-Relation, falls sie noch nicht geladen ist
        $this->user->loadMissing('roles:id,name,is_manager');

        // Prüfe auf Änderungen bei den Manager-Rollen, bevor synchronisiert wird
        if ($this->user->relationLoaded('roles')) {
            $this->checkRoleChangesForManager($this->user);
        }

        // Synchronisiere die Rollen und Teams
        $this->user->roles()->sync($this->selectedRoles);
        $this->user->teams()->sync($this->selectedTeams);
    }

    /**
     * Setzt das Formular zurück und schließt das Modal.
     * Bereinigt zusätzlich alle Cache-Properties, um Speicher freizugeben.
     */
    public function closeEditEmployeeModal(): void
    {
        $this->modal('edit-employee')->close();

        $this->js("
        setTimeout(() => {
              \$wire.resetFormData();
            }, 1);
        ");

        $this->showEditModal = false;
    }

    public function resetFormData(): void
    {
        $this->resetErrorBag();

        $this->reset([
            'gender', 'name', 'last_name', 'email', 'selectedTeams',
            'department', 'supervisor', 'selectedRoles', 'profession',
            'stage', 'joined_at', 'employee_status', 'model_status',
            'teams', 'departments', 'roles',
            'professions', 'stages', 'supervisors'
        ]);
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
        }
    }

    /**
     * Lädt Supervisors als Array
     */
    private function loadSupervisors(): array
    {
        $supervisors = User::getCompanyManagers($this->companyId);

        return $supervisors
            ->reject(fn($sup) => $sup->id === $this->authUserId)
            ->map(fn($sup) => [
                'id' => $sup->id,
                'name' => $sup->name,
                'last_name' => $sup->last_name,
                'full_name' => $sup->name . ' ' . $sup->last_name,
                'profile_photo_path' => $sup->profile_photo_path
            ])
            ->toArray();
    }

    public function render(): View
    {
        return view('livewire.alem.employee.edit');
    }
}
