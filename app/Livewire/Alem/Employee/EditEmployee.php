<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Helper\Secure\HandleCatchError;
use App\Livewire\Alem\Employee\Helper\Secure\ValidateEmployee;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Models\Alem\Employee;
use App\Models\Spatie\Role;
use App\Models\User;
use App\Traits\Employee\EmployeeStatusOptions;
use App\Traits\Enum\GenderOptions;
use App\Traits\Model\ModelStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Lade nur was initial benötigt wird
        $this->loadRelationsData([
            'teams', 'departments', 'roles', 'professions', 'stages', 'supervisors'
        ]);

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

//    /**
//     * Synchronisiert die Rollen und Teams des Benutzers.
//     */
//    private function syncRelations(): void
//    {
//        $this->user->roles()->sync($this->selectedRoles);
//        $this->user->teams()->sync($this->selectedTeams);
//
//        // Nach der Synchronisation ggf. Supervisors neu laden
//        if ($this->hasManagerRoleInList($this->selectedRoles) !== $this->user->hasManagerRole()) {
//            $this->forceReloadCollection('supervisors');
//        }
//    }

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
            'gender', 'name', 'last_name', 'email', 'selectedTeams',
            'department', 'supervisor', 'selectedRoles', 'profession',
            'stage', 'joined_at', 'employee_status', 'model_status',
        ]);

        $this->resetDropdownRelationsData();
    }

    public function render(): View
    {
        return view('livewire.alem.employee.edit');
    }

    /**
     * Prüft ob eine Rollenliste Manager-Rollen enthält
     *
     * @param array $roleIds Array von Rollen-IDs
     * @return bool
     */
    protected function hasManagerRoleInList(array $roleIds): bool
    {
        // Nutze die bereits geladenen Rollen aus $this->roles
        if (!empty($this->roles)) {
            return collect($this->roles)
                ->whereIn('id', $roleIds)
                ->where('is_manager', true)
                ->isNotEmpty();
        }

        // Fallback auf DB-Query nur wenn roles nicht geladen
        return Role::whereIn('id', $roleIds)
            ->where('is_manager', true)
            ->exists();
    }

    private function syncRelations(): void
    {
        // Erfasse Manager-Status VOR der Änderung
        $wasManager = $this->user->hasManagerRole();
        $oldManagerRoleIds = $this->user->roles()
            ->where('is_manager', true)
            ->pluck('id')
            ->toArray();

        \Debugbar::info("User was manager: " . ($wasManager ? 'YES' : 'NO'));

        // Sync Roles
        $this->user->roles()->sync($this->selectedRoles);

        // Lade User neu mit frischen Rollen
        $this->user->load('roles');

        // Prüfe ob sich Manager-Status geändert hat
        $isManagerNow = $this->user->hasManagerRole();
        $newManagerRoleIds = Role::whereIn('id', $this->selectedRoles)
            ->where('is_manager', true)
            ->pluck('id')
            ->toArray();

        \Debugbar::info("User is manager now: " . ($isManagerNow ? 'YES' : 'NO'));

        // Wenn sich Manager-Status geändert hat, Cache manuell leeren
        if ($wasManager !== $isManagerNow) {
            \Debugbar::warning("MANAGER STATUS CHANGED! Clearing cache manually...");

            // Direkt Cache leeren
            $cacheKey = "users:company:{$this->user->company_id}:managers";
            \Cache::forget($cacheKey);

            // Alternative: Nutze die User Model Methode
            User::clearManagerCache($this->user->company_id);

            // Force reload supervisors
            $this->forceReloadCollection('supervisors');

            \Debugbar::info("Cache cleared for company {$this->user->company_id}");
        }
        // Sync Teams
        $this->user->teams()->sync($this->selectedTeams);
    }
}
