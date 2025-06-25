<?php

namespace App\Livewire\Alem\Employee;

use App\Livewire\Alem\Employee\Helper\Secure\HandleCatchError;
use App\Livewire\Alem\Employee\Helper\Secure\ValidateEmployee;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Models\Alem\Employee;
use App\Models\User;
use App\Traits\Employee\EmployeeStatusOptions;
use App\Traits\Enum\GenderOptions;
use App\Traits\Model\ModelStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
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
    use AuthUserTeamCompanyId, WithDropDownRelations, ValidateEmployee, HandleCatchError;
    use ModelStatusOptions, EmployeeStatusOptions, GenderOptions;


    #[Locked]
    public ?int $userId = null;

    /** Modal-Status: Braucht jedes Komponent mit einem Modal */
    public bool $showEditModal = false;

    protected function shouldCheckModalState(): bool
    {
        return true;
    }

    protected function isModalOpen(): bool
    {
        return $this->showEditModal;
    }
    /** Modal-Status mit Funktionen */

    // User identification
    public ?User $user = null;

    /** Benutzer-Felder */
    public ?string $gender = null;
    public ?string $name = null;
    public ?string $email = null;

    public array $selectedTeams = [];
    public ?int $department = null;
    public ?int $supervisor = null;
    public array $selectedRoles = [];

    public ?int $profession = null;
    public ?int $stage = null;
    public ?string $joined_at = null;

    public ?string $status = null;
    public ?string $model_status = null;

    /** Original Daten des Users aus der Datenbank für Vergleiche */
    public array $originalData = [];


    #[On('edit-employee-modal')]
    public function openEditEmployeeModal($userId): void
    {
        // $this->authorize('update', User::class);
        $this->userId = $userId;

        // Hier werden die Realtion des Users Employee geladen zu denen er gehört.
        $this->user = User::with([
            'teams:id,name',
            'roles:id,name,is_manager',
        ])
            ->select([
                'id', 'name', 'email', 'gender', 'model_status',
                'status', 'department_id', 'supervisor_id',
                'profession_id', 'stage_id', 'joined_at', 'user_type',
                'company_id','manager'
            ])
            ->findOrFail($this->userId);

        $this->loadEmployeeData();

        // hier werden die Dropdown-Relationen geladen, die zu Auth User gehören.
        $this->loadRelationsData([
            'teams', 'departments', 'roles', 'professions', 'stages', 'supervisors'
        ]);

        $this->showEditModal = true;
    }

    /**
     * Befülle die Form mit User Employee Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    protected function loadEmployeeData(): void
    {
        if (!$this->user) return;

        // WICHTIG: Speichere Original-Daten in EINEM public Array
        $this->originalData = [
            'email' => $this->user->email,
            'teamIds' => $this->user->teams->pluck('id')->toArray(),
            'roleIds' => $this->user->roles->pluck('id')->toArray(),
        ];


        $this->gender = $this->user->gender?->value;
        $this->name = $this->user->name;
        $this->email = $this->user->email;

        // Setze selected Arrays
        $this->selectedTeams = $this->originalData['teamIds'];
        $this->selectedRoles = $this->originalData['roleIds'];

        $this->department = $this->user->department_id;
        $this->supervisor = $this->user->supervisor_id;
        $this->profession = $this->user->profession_id;
        $this->stage = $this->user->stage_id;

        $this->joined_at = $this->user->joined_at?->format('Y-m-d');

        // Status ENUM
        $this->status = $this->user->status?->value;
        $this->model_status = $this->user->model_status?->value;

    }

    /**
     * Aktualisiert die Benutzer- und Mitarbeiterdaten in der Datenbank.
     */
    public function updateEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // ✅ Golden Path: Eloquent Update
                $this->user->update([
                    'gender' => $this->gender,
                    'name' => $this->name,
                    'email' => $this->email,
                    'department_id' => $this->department,
                    'profession_id' => $this->profession,
                    'supervisor_id' => $this->supervisor,
                    'stage_id' => $this->stage,
                    'joined_at' => $this->joined_at,
                    'status' => $this->status,
                    'model_status' => $this->model_status,
                ]);

                $this->updateEmployeeData();
                $this->updateTeamsRoles();

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
     * Nur sicherstellen dass Employee existiert
     * Nur Existenz sichern → firstOrCreate()
     * Existenz sichern + Update → updateOrCreate()
     */
    private function updateEmployeeData(): void
    {
        Employee::firstOrCreate(['user_id' => $this->userId]);
    }

    /**
     * Email Check für Validation
     */
    public function emailHasChanged(): bool
    {
        return $this->email !== ($this->originalData['email'] ?? '');
    }

    /**
     * Haupt-Methode mit optionaler Team-Sync
     */
    private function updateTeamsRoles(): void
    {
        $this->syncTeams();
        $this->syncRolesWithManagerCheck();
    }

    /**
     * Helper: Check Manager in Roles ohne DB Query
     */
    private function checkManagerInRoles(array $roleIds): bool
    {
        return collect($this->roles)
            ->whereIn('id', $roleIds)
            ->contains('is_manager', true);
    }

    /**
     * Helper: Arrays vergleichen
     */
    private function arraysAreDifferent(array $array1, array $array2): bool
    {
        return count(array_diff($array1, $array2)) > 0 ||
            count(array_diff($array2, $array1)) > 0;
    }

// Außerdem: In syncTeams() musst du diese Zeile korrigieren:
    private function syncTeams(): void
    {

        // Verwende stattdessen:
        $originalTeamIds = $this->originalData['teamIds'] ?? [];

        $teamsChanged = $this->arraysAreDifferent($originalTeamIds, $this->selectedTeams);

        if ($teamsChanged) {
            $this->user->teams()->sync($this->selectedTeams);
        }
    }

    /**
     * Optimierte syncRolesWithManagerCheck - KEINE zusätzliche Query!
     */
    private function syncRolesWithManagerCheck(): void
    {
        $originalRoleIds = $this->originalData['roleIds'] ?? [];

        $rolesChanged = $this->arraysAreDifferent($originalRoleIds, $this->selectedRoles);

        if (!$rolesChanged) {
            return;
        }

        // Manager Status aus bereits geladenen Daten
        $oldHasManager = $this->checkManagerInRoles($originalRoleIds);

        // Sync Rollen
        $this->user->roles()->sync($this->selectedRoles);

        // Neuer Manager Status
        $newHasManager = $this->checkManagerInRoles($this->selectedRoles);

        // Update manager field im User Model
        if ($oldHasManager !== $newHasManager) {
            $this->user->update(['manager' => $newHasManager]);

            // Cache clear und Collection reload
            User::clearManagerCache($this->user->company_id);
            $this->forceReloadCollection('supervisors');
        }
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
              \$wire.resetFormInputs();
            }, 1);
        ");

        $this->showEditModal = false;
    }

    public function resetFormInputs(): void
    {
        $this->resetErrorBag();

        $this->reset([
            'gender', 'name', 'email', 'selectedTeams',
            'department', 'supervisor', 'selectedRoles', 'profession',
            'stage', 'joined_at', 'status', 'model_status',
            'originalData'
        ]);

        $this->resetDropdownRelationsData();
    }

    public function render(): View
    {
        return view('livewire.alem.employee.edit');
    }

}
