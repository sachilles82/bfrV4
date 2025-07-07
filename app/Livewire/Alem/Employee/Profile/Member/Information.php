<?php

namespace App\Livewire\Alem\Employee\Profile\Member;

use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Livewire\Alem\Employee\Profile\Member\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Member\Helper\ValidateMemberInformation;
use App\Models\User;
use App\Traits\Employee\EmployeeStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
class Information extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId;
    use ValidateMemberInformation, HandleCatchError;
    use WithDropDownRelations;
    use EmployeeStatusOptions;

    #[Locked]
    public int $userId;

    /** Form fields */
    public ?string $status = null;
    public ?int $department = null;
    public ?int $profession = null;
    public ?int $stage = null;
    public ?int $supervisor = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    /** Original Daten für Vergleiche */
    public array $originalData = [];

    public function mount(int $userId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->userId = $userId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        $this->loadMemberData();

        // Lade Dropdown-Daten
        $this->loadRelationsData([
            'teams', 'departments', 'roles', 'supervisors', 'stages', 'professions'
        ]);
    }

    /**
     * User als Computed Property
     * Wird automatisch gecached und nach Validation Error neu geladen
     */
    #[Computed]
    public function user(): User
    {
        return User::with([
            'teams:id,name',
            'roles:id,name,is_manager',
        ])
            ->select([
                'id', 'status', 'user_type',
                'department_id', 'profession_id', 'stage_id',
                'company_id', 'manager', 'supervisor_id'
            ])
            ->findOrFail($this->userId);
    }

    /**
     * Befülle die Form mit Member Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadMemberData(): void
    {
        $user = $this->user;

        // Original-Daten speichern - KEINE empty strings, nur null
        $this->originalData = [
            'teamIds' => $user->teams->pluck('id')->toArray(),
            'roleIds' => $user->roles->pluck('id')->toArray(),
            'department_id' => $user->department_id,
            'profession_id' => $user->profession_id,
            'stage_id' => $user->stage_id,
            'supervisor_id' => $user->supervisor_id,
            'status' => $user->status?->value,
        ];

        // Setze Form-Felder NUR mit den originalData
        $this->selectedTeams = $this->originalData['teamIds'];
        $this->selectedRoles = $this->originalData['roleIds'];
        $this->department = $this->originalData['department_id'];
        $this->profession = $this->originalData['profession_id'];
        $this->stage = $this->originalData['stage_id'];
        $this->supervisor = $this->originalData['supervisor_id'];
        $this->status = $this->originalData['status'];
    }

    /**
     * Aktualisiert die Member Information in der Datenbank.
     * Validiert nur die geänderten Felder für bessere Performance
     */
    public function updateMemberInformation(): void
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

                if ($this->departmentHasChanged()) {
                    $updateData['department_id'] = $this->department;
                }
                if ($this->professionHasChanged()) {
                    $updateData['profession_id'] = $this->profession;
                }
                if ($this->stageHasChanged()) {
                    $updateData['stage_id'] = $this->stage;
                }
                if ($this->supervisorHasChanged()) {
                    $updateData['supervisor_id'] = $this->supervisor;
                }
                if ($this->statusHasChanged()) {
                    $updateData['status'] = $this->status;
                }

                // Update nur wenn Felder geändert wurden
                if (!empty($updateData)) {
                    $this->user->update($updateData);
                }

                // Teams und Roles synchronisieren
                $this->updateTeamsRoles();
            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            // WICHTIG: Clear Computed Property Cache nach Update
            unset($this->user);

            $this->dispatch('member-information-updated');

            Flux::toast(
                text: __('Member information updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleEditingError($e);
        }
    }

    /**
     * Aktualisiert die Original-Daten nach erfolgreichem Speichern
     */
    private function updateOriginalDataAfterSave(): void
    {
        $this->originalData = [
            'teamIds' => $this->selectedTeams,
            'roleIds' => $this->selectedRoles,
            'department_id' => $this->department,
            'profession_id' => $this->profession,
            'stage_id' => $this->stage,
            'supervisor_id' => $this->supervisor,
            'status' => $this->status,
        ];
    }

    /**
     * Haupt-Methode für Teams und Roles Update
     */
    private function updateTeamsRoles(): void
    {
        $this->syncTeams();
        $this->syncRolesWithManagerCheck();
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

    public function placeholder(): string
    {
        return view('livewire.placeholders.employee.member-information');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.member.information', [
            'user' => $this->user
        ]);
    }
}
