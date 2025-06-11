<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Helper\Secure\HandleCatchError;
use App\Livewire\Alem\Employee\Helper\Secure\ValidateEmployee;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Models\Alem\Employee;
use App\Models\User;
use App\Traits\Employee\EmployeeStatusOptions;
use App\Traits\Enum\GenderOptions;
use App\Traits\Model\ModelStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
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
        ]);

        $this->resetDropdownCollections();
    }

    /**
     * Prüft auf Änderungen bei Manager-Rollen und leert ggf. den Manager-Cache.
     *
     * @param User $user Der Benutzer (mit geladenen Rollen).
     * @return void
     */
    private function checkRoleChangesForManager(User $user): void
    {
        // Validierung und Vorbereitung
        if (!$this->companyId) {
            return;
        }

        // Stelle sicher, dass Rollen geladen sind
        if (!$user->relationLoaded('roles')) {
            $user->loadMissing('roles:id,is_manager');
            if (!$user->relationLoaded('roles')) {
                return;
            }
        }

        // Stelle sicher, dass verfügbare Rollen vorhanden sind
        if (empty($this->roles)) {
            $this->loadRelationsData();
            if (empty($this->roles)) {
                return;
            }
        }

        // Alte Manager-Rollen-IDs
        $oldManagerRoleIds = $user->roles
            ->where('is_manager', true)
            ->pluck('id')
            ->sort()
            ->values()
            ->all();

        // Neue Manager-Rollen-IDs (optimiert mit Array-Funktionen)
        $newManagerRoleIds = array_values(
            array_filter(
                array_map(
                    fn($role) => ($role['is_manager'] ?? false) && in_array($role['id'], $this->selectedRoles, true) ? $role['id'] : null,
                    $this->roles
                ),
                fn($id) => $id !== null
            )
        );
        sort($newManagerRoleIds);

        // Cache leeren bei Änderungen
        if ($oldManagerRoleIds !== $newManagerRoleIds) {
            User::flushManagerCache($this->companyId);
            $this->supervisors = [];
        }
    }

    public function render(): View
    {
        return view('livewire.alem.employee.edit');
    }
}
