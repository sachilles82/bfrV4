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
    public int $employeeId;

    public ?User $employee = null;

    /** User form fields */
    public ?string $gender = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone_1 = null;
    public ?string $model_status = null;
    public ?int $department = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    /** Original Daten des Users aus der Datenbank für Vergleiche */
    public array $originalData = [];

    public function mount(int $employeeId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->employeeId = $employeeId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Lade Employee mit allen benötigten Relations
        $this->employee = User::with([
            'teams:id,name',
            'roles:id,name,is_manager',
        ])
            ->select([
                'id', 'name', 'email', 'gender', 'model_status',
                'department_id', 'phone_1', 'company_id', 'manager'
            ])
            ->findOrFail($this->employeeId);

        $this->loadEmployeeData();

        // Lade Dropdown-Daten
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
        if (!$this->employee) return;

        // WICHTIG: Speichere Original-Daten in EINEM public Array
        $this->originalData = [
            'name' => $this->employee->name,
            'email' => $this->employee->email,
            'phone_1' => $this->employee->phone_1,
            'gender' => $this->employee->gender?->value,
            'teamIds' => $this->employee->teams->pluck('id')->toArray(),
            'roleIds' => $this->employee->roles->pluck('id')->toArray(),
            'department_id' => $this->employee->department_id,
            'model_status' => $this->employee->model_status?->value,
        ];

        $this->gender = $this->employee->gender?->value;
        $this->name = $this->employee->name;
        $this->email = $this->employee->email;
        $this->phone_1 = $this->employee->phone_1 ?? '';

        // Setze selected Arrays
        $this->selectedTeams = $this->originalData['teamIds'];
        $this->selectedRoles = $this->originalData['roleIds'];

        $this->department = $this->employee->department_id;
        $this->model_status = $this->employee->model_status?->value;
    }

    /**
     * Aktualisiert die Benutzer- und Mitarbeiterdaten in der Datenbank.
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

        $this->validate();

        try {
            DB::transaction(function () {
                // Lade Employee fresh für Update mit Relations für Manager Check
                $employee = User::with('roles:id,name,is_manager')->findOrFail($this->employeeId);

                // ✅ Golden Path: Eloquent Update
                $employee->update([
                    'gender' => $this->gender,
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_1' => $this->phone_1,
                    'department_id' => $this->department,
                    'model_status' => $this->model_status,
                ]);

                // Für die sync Methoden
                $this->employee = $employee;

                $this->updateTeamsRoles();
            });

//            // ✅ LÖSUNG: Lade Employee FRESH ohne Cache
//            $this->employee = User::with([
//                'teams:id,name',
//                'roles:id,name,is_manager',
//            ])
//                ->select([
//                    'id', 'name', 'email', 'gender', 'model_status',
//                    'department_id', 'phone_1', 'company_id', 'manager'
//                ])
//                ->findOrFail($this->employeeId);
//
//            // Aktualisiere die Original-Daten nach erfolgreichem Update
//            $this->loadEmployeeData();

            $this->updateOriginalDataAfterSave();

            $this->dispatch('employee-updated');

            Flux::toast(
                text: __('Employee Account Details updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            \Log::error('updateEmployee failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->handleEditingError($e);
        }
    }
    private function updateOriginalDataAfterSave(): void
    {
        // Update nur die originalData, ohne die Form-Felder zu überschreiben
        $this->originalData = [
            'name' => $this->name,
            'email' => $this->email,
            'phone_1' => $this->phone_1,
            'gender' => $this->gender,
            'teamIds' => $this->selectedTeams,  // Verwende die aktuellen Werte
            'roleIds' => $this->selectedRoles,  // Verwende die aktuellen Werte
            'department_id' => $this->department,
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
     * Helper: Arrays vergleichen
     */
    private function arraysAreDifferent(array $array1, array $array2): bool
    {
        return count(array_diff($array1, $array2)) > 0 ||
            count(array_diff($array2, $array1)) > 0;
    }

    /**
     * Teams nur synchronisieren wenn sich etwas geändert hat
     */
    private function syncTeams(): void
    {
        $originalTeamIds = $this->originalData['teamIds'] ?? [];
        $teamsChanged = $this->arraysAreDifferent($originalTeamIds, $this->selectedTeams);

        if ($teamsChanged) {
            $this->employee->teams()->sync($this->selectedTeams);
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
        $this->employee->roles()->sync($this->selectedRoles);

        // Neuer Manager Status
        $newHasManager = $this->checkManagerInRoles($this->selectedRoles);

        // Update manager field im User Model
        if ($oldHasManager !== $newHasManager) {
            $this->employee->update(['manager' => $newHasManager]);

            // Cache clear und Collection reload
            User::clearManagerCache($this->employee->company_id);
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
            'employee' => $this->employee ?? null
        ]);
    }
}
