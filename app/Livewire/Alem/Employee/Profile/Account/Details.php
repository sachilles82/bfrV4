<?php

namespace App\Livewire\Alem\Employee\Profile\Account;

use App\Livewire\Alem\Employee\Helper\Secure\HandleCatchError;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Livewire\Alem\Employee\Profile\Account\Helper\ValidateAccountDetails;
use App\Models\User;
use App\Traits\Employee\EmployeeStatusOptions;
use App\Traits\Enum\GenderOptions;
use App\Traits\Model\ModelStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
class Details extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId;
    use ValidateAccountDetails, HandleCatchError;
    use WithDropDownRelations;
    use ModelStatusOptions, EmployeeStatusOptions, GenderOptions;

    #[Locked]
    public int $userId;

    public ?User $user = null; // der geladene User

    /** User form fields */
    public ?string $gender = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone_1 = null;
    public ?string $model_status = null;
    public ?int $department = null;
    public ?int $supervisor = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    /** Original Daten des Users aus der Datenbank für Vergleiche */
    public array $originalData = [];

    public function mount(int $userId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->userId = $userId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Lade userId mit allen benötigten Relations
        $this->user = User::with([
            'teams:id,name',
            'roles:id,name,is_manager',
        ])
            ->select([
                'id', 'name', 'email', 'gender', 'model_status',
                'department_id', 'phone_1', 'company_id', 'manager','supervisor_id'
            ])
            ->findOrFail($this->userId);

        $this->loadEmployeeData();

//        // Lade Dropdown-Daten
        $this->loadRelationsData([
            'teams', 'departments', 'roles', 'supervisors'
        ]);
    }

    /**
     * Befülle die Form mit User Employee Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadEmployeeData(): void
    {
        if (!$this->user) return;

        // WICHTIG: Speichere Original-Daten in EINEM public Array
        $this->originalData = [
            'gender' => $this->user->gender?->value,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone_1' => $this->user->phone_1,
            'teamIds' => $this->user->teams->pluck('id')->toArray(),
            'roleIds' => $this->user->roles->pluck('id')->toArray(),
            'department_id' => $this->user->department_id,
            'supervisor_id' => $this->user->supervisor_id,
            'model_status' => $this->user->model_status?->value,
        ];

        $this->gender = $this->user->gender?->value;
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->phone_1 = $this->user->phone_1 ?? '';

        // Setze selected Arrays
        $this->selectedTeams = $this->originalData['teamIds'];
        $this->selectedRoles = $this->originalData['roleIds'];

        $this->department = $this->user->department_id;
        $this->supervisor = $this->user->supervisor_id;
        $this->model_status = $this->user->model_status?->value;
    }


    /**
     * Aktualisiert die Benutzer- und Mitarbeiterdaten in der Datenbank.
     * Validiert nur die geänderten Felder für bessere Performance
     */
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
                // Erstelle Update-Array nur mit geänderten Feldern
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
                if ($this->phoneHasChanged()) {
                    $updateData['phone_1'] = $this->phone_1;
                }
                if ($this->departmentHasChanged()) {
                    $updateData['department_id'] = $this->department;
                }
                if ($this->supervisorHasChanged()) {
                    $updateData['supervisor_id'] = $this->supervisor;
                }
                if ($this->modelStatusHasChanged()) {
                    $updateData['model_status'] = $this->model_status;
                }

                // Update nur wenn Felder geändert wurden
                if (!empty($updateData)) {
                    $this->user->update($updateData);
                }

                // Teams und Roles - die sync() Methoden brauchen $this->user
                $this->updateTeamsRoles();

            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            $this->dispatch('employee-updated');

            Flux::toast(
                text: __('Employee Account Details updated successfully.'),
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
            'phone_1' => $this->phone_1,
            'teamIds' => $this->selectedTeams,
            'roleIds' => $this->selectedRoles,
            'department_id' => $this->department,
            'supervisor_id' => $this->supervisor,
            'model_status' => $this->model_status,
        ];
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

    public function placeholder(): string
    {
        return view('livewire.placeholders.employee.details');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.account.details', [
            'user' => $this->user ?? null
        ]);
    }
}
