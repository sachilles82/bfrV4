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
    /** Benutzer-Felder */
    public ?Gender $gender = null;
    public ?string $name = null;
    public ?string $email = null;

    public array $selectedTeams = [];
    public ?int $department = null;
    public ?int $supervisor = null;
    public array $selectedRoles = [];

    public ?int $profession = null;
    public ?int $stage = null;
    public ?Carbon $joined_at = null;

    public ?EmployeeStatus $status = null;
    public ?ModelStatus $model_status = null;


    #[On('edit-employee-modal')]
    public function openEditEmployeeModal($userId): void
    {
        // $this->authorize('update', User::class);

        $this->userId = $userId;

        // Hier werden die Realtion des Users Employee geladen zu denen er gehört.
        $this->user = User::with([
            'employee:id',
            'teams:id,name',
            'roles:id,name,is_manager',
            'department:id,name'
        ])->findOrFail($this->userId);

        $this->loadEmployeeData();

        // hier werden die Dropdown-Relationen geladen, die zu Auth User gehören.
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
        $this->email = $this->user->email;

        $this->selectedTeams = $this->user->teams->pluck('id')->toArray();
        $this->department = $this->user->department_id;
        $this->supervisor = $this->user->supervisor_id;
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();
        $this->profession = $this->user->profession_id;
        $this->stage = $this->user->stage_id;

        $this->joined_at = $this->user->joined_at;

        // Status ENUM
        $this->status = $this->user->status;
        $this->model_status = $this->user->model_status;

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
                    'gender' => $this->gender,
                    'name' => $this->name,
                    'email' => $this->email,
                    'department_id' => $this->department,
                    'profession_id' => $this->profession,
                    'supervisor_id' => $this->supervisor,
                    'stage_id' => $this->stage,
                    'joined_at' => $this->joined_at?->toDateString(),
                    'status' => $this->status,
                    'model_status' => $this->model_status,
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
        );
    }

    /**
     * Synchronisiert Rollen und Teams des Users
     *
     * @throws \Throwable
     */
    private function syncRelations(): void
    {
        DB::transaction(function (): void {
            // Cache Manager-Status vor Änderung
            $wasManager = $this->user->hasManagerRole();

            // Batch-Synchronisation
            $this->user->roles()->sync($this->selectedRoles);
            $this->user->teams()->sync($this->selectedTeams);

            // Handle Manager-Status-Änderung
            if ($wasManager !== $this->user->hasManagerRole()) {
                User::clearManagerCache($this->user->company_id);
                $this->forceReloadCollection('supervisors');
            }
        });
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
        ]);

        $this->resetDropdownRelationsData();
    }

    public function render(): View
    {
        return view('livewire.alem.employee.edit');
    }

}
