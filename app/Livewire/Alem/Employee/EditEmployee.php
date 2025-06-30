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
    use AuthUserTeamCompanyId;
    use ValidateEmployee, HandleCatchError;
    use WithDropDownRelations;
    use ModelStatusOptions, EmployeeStatusOptions, GenderOptions;

    #[Locked]
    public ?int $userId = null;

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
                'profession_id', 'stage_id', 'joined_at',
                //?? für was user_type
                'user_type',
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
            'gender' => $this->user->gender?->value,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'teamIds' => $this->user->teams->pluck('id')->toArray(),
            'department_id' => $this->user->department_id,
            'supervisor_id' => $this->user->supervisor_id,
            'roleIds' => $this->user->roles->pluck('id')->toArray(),
            'profession_id' => $this->user->profession_id,
            'stage_id' => $this->user->stage_id,
            'joined_at' => $this->user->joined_at?->format('Y-m-d'),

            // Status ENUM
            'status' => $this->user->status?->value,
            'model_status' => $this->user->model_status?->value,
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

//    /**
//     * Aktualisiert die Benutzer- und Mitarbeiterdaten in der Datenbank.
//     */
//    public function updateEmployee(): void
//    {
//        // Type-Casting direkt am Anfang
//        $this->selectedRoles = collect($this->selectedRoles)
//            ->map(fn($role) => (int) $role)
//            ->toArray();
//
//        $this->selectedTeams = collect($this->selectedTeams)
//            ->map(fn($team) => (int) $team)
//            ->toArray();
//
//        // Prüfe ob überhaupt Änderungen vorliegen
//        if (!$this->hasAnyChanges()) {
//            Flux::toast(
//                text: __('No changes detected.'),
//                heading: __('No Update'),
//                variant: 'warning'
//            );
//            return;
//        }
//
//        // WICHTIG: Hole die geänderten Felder VOR dem Update
//        $changedFields = $this->getChangedFields();
//
//        // WICHTIG: Validiere NUR die geänderten Felder
//        $this->validateOnlyChanged();
//
//        try {
//            DB::transaction(function () {
//                $updateData = [];
//
//                if ($this->genderHasChanged()) {
//                    $updateData['gender'] = $this->gender;
//                }
//                if ($this->nameHasChanged()) {
//                    $updateData['name'] = $this->name;
//                }
//                if ($this->emailHasChanged()) {
//                    $updateData['email'] = $this->email;
//                }
//                if ($this->departmentHasChanged()) {
//                    $updateData['department_id'] = $this->department;
//                }
//                if ($this->supervisorHasChanged()) {
//                    $updateData['supervisor_id'] = $this->supervisor;
//                }
//                if ($this->professionHasChanged()) {
//                    $updateData['profession_id'] = $this->profession;
//                }
//                if ($this->stageHasChanged()) {
//                    $updateData['stage_id'] = $this->stage;
//                }
//                if ($this->joinedAtHasChanged()) {
//                    $updateData['joined_at'] = $this->joined_at;
//                }
//                if ($this->statusHasChanged()) {
//                    $updateData['status'] = $this->status;
//                }
//                if ($this->modelStatusHasChanged()) {
//                    $updateData['model_status'] = $this->model_status;
//                }
//
//                // Update nur wenn Felder geändert wurden
//                if (!empty($updateData)) {
//                    $this->user->update($updateData);
//                }
//
//                // Teams und Roles - die sync() Methoden brauchen $this->user
//                $this->updateEmployeeData();
//                $this->updateTeamsRoles();
//            });
//
//            // Aktualisiere Original-Daten nach erfolgreichem Update
//            $this->updateOriginalDataAfterSave();
//
//            $this->closeEditEmployeeModal();
//            $this->dispatch('employee-updated');
//
//            // Erstelle eine lesbare Liste der Änderungen
//            $updatedFieldsList = $this->formatChangedFieldsForDisplay($changedFields);
//
//            Flux::toast(
//                text: __('Updated fields: ') . $updatedFieldsList,
//                heading: __('Employee updated successfully'),
//                variant: 'success'
//            );
//
//        } catch (\Throwable $e) {
//            \Log::error('updateEmployee failed', [
//                'error' => $e->getMessage(),
//                'trace' => $e->getTraceAsString(),
//            ]);
//            $this->handleEditingError($e);
//        }
//    }
//
//    /**
//     * Formatiert die geänderten Felder für die Anzeige im Toast
//     */
//    private function formatChangedFieldsForDisplay(array $changedFields): string
//    {
//        $fieldLabels = [
//            'name' => __('Name'),
//            'email' => __('Email'),
//            'phone_1' => __('Phone'),
//            'gender' => __('Gender'),
//            'department_id' => __('Department'),
//            'supervisor_id' => __('Supervisor'),
//            'profession_id' => __('Profession'),
//            'stage_id' => __('Stage'),
//            'joined_at' => __('Joined At'),
//            'status' => __('Status'),
//            'model_status' => __('Model Status'),
//            'teams' => __('Teams'),
//            'roles' => __('Roles'),
//        ];
//
//        $displayFields = [];
//
//        foreach ($changedFields as $field => $changes) {
//            $label = $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field));
//
//            // Optional: Zeige auch die Werte an
//            if (in_array($field, ['teams', 'roles'])) {
//                // Bei Arrays zeige Anzahl
//                $oldCount = count($changes['old']);
//                $newCount = count($changes['new']);
//                $displayFields[] = "{$label} ({$oldCount} → {$newCount})";
//            } else {
//                // Bei einzelnen Werten
//                $displayFields[] = $label;
//                // Oder mit Werten: $displayFields[] = "{$label}: {$changes['old']} → {$changes['new']}";
//            }
//        }
//
//        return implode(', ', $displayFields);
//    }
    public function updateEmployee(): void
    {
        // Type-Casting direkt am Anfang
        $this->selectedRoles = collect($this->selectedRoles)
            ->map(fn($role) => (int) $role)
            ->toArray();

        $this->selectedTeams = collect($this->selectedTeams)
            ->map(fn($team) => (int) $team)
            ->toArray();

        // Prüfe ob überhaupt Änderungen vorliegen
        if (!$this->hasAnyChanges()) {
            Flux::toast(
                text: __('No changes detected.'),
                heading: __('No Update'),
                variant: 'warning'
            );
            return;
        }

        // WICHTIG: Validiere NUR die geänderten Felder
        $this->validateOnlyChanged();

        try {
            DB::transaction(function () {

                $updateData = [];

                if ($this->genderHasChanged()) {
                    $updateData['gender'] = $this->gender;
                }
                if ($this->nameHasChanged()) {
                    $updateData['name'] = $this->name;
                }
                if ($this->emailHasChanged()) {
                    $updateData['email'] = $this->email;
                }
//                if ($this->teamHasChanged()) {
//                    $updateData['team_ids'] = $this->selectedTeams;
//                }
                if ($this->departmentHasChanged()) {
                    $updateData['department_id'] = $this->department;
                }
                if ($this->supervisorHasChanged()) {
                    $updateData['supervisor_id'] = $this->supervisor;
                }
                if ($this->professionHasChanged()) {
                    $updateData['profession_id'] = $this->profession;
                }
                if ($this->stageHasChanged()) {
                    $updateData['stage_id'] = $this->stage;
                }
                if ($this->joinedAtHasChanged()) {
                    $updateData['joined_at'] = $this->joined_at;
                }
                if ($this->statusHasChanged()) {
                    $updateData['status'] = $this->status;
                }
                if ($this->modelStatusHasChanged()) {
                    $updateData['model_status'] = $this->model_status;
                }
                // Update nur wenn Felder geändert wurden
                if (!empty($updateData)) {
                    $this->user->update($updateData);
                }

                // Teams und Roles - die sync() Methoden brauchen $this->user
                $this->updateEmployeeData();

                $this->updateTeamsRoles();

            });;

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

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
     * Prüft, ob Änderungen an den Feldern vorgenommen wurden
     */
    private function updateOriginalDataAfterSave(): void
    {
        // Update nur die originalData, ohne die Form-Felder zu überschreiben
        $this->originalData = [
            'gender' => $this->gender,
            'name' => $this->name,
            'email' => $this->email,
            'teamIds' => $this->selectedTeams,
            'department_id' => $this->department,
            'supervisor_id' => $this->supervisor,
            'roleIds' => $this->selectedRoles,
            'profession_id' => $this->profession,
            'stage_id' => $this->stage,
            'joined_at' => $this->joined_at,
            'status' => $this->status,
            'model_status' => $this->model_status,

        ];
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
        // Nutze die geladenen Dropdown-Daten
        if (empty($this->dropdownRelations['roles'])) {
            return false;
        }

        return collect($this->dropdownRelations['roles'])
            ->whereIn('id', $roleIds)
            ->contains('is_manager', true);
    }



    /**
     * Teams nur synchronisieren wenn sich etwas geändert hat
     */
    private function syncTeams(): void
    {
        if ($this->teamsHaveChanged()) {
            $this->user->teams()->sync($this->selectedTeams);
        }
    }

    /**
     * Optimierte syncRolesWithManagerCheck - KEINE zusätzliche Query!
     */
    private function syncRolesWithManagerCheck(): void
    {
        if (!$this->rolesHaveChanged()) {
            return;
        }

        // Manager Status aus bereits geladenen Daten
        $originalRoleIds = $this->originalData['roleIds'] ?? [];
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
