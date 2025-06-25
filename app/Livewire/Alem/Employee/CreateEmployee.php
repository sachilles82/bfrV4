<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateEmployee extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId, WithDropDownRelations, ValidateEmployee, HandleCatchError;
    use ModelStatusOptions, EmployeeStatusOptions, GenderOptions;

    /** Modal-Status mit Funktionen */
    public bool $showCreateModal = false;

    protected function shouldCheckModalState(): bool
    {
        return true;
    }

    protected function isModalOpen(): bool
    {
        return $this->showCreateModal;
    }
    /** Modal-Status mit Funktionen */

    /** Benutzer-Felder */
    public ?int $userId = null;
    public ?string $gender = null;
    public ?string $name = null;
    public ?string $email = null;

    public array $selectedTeams = [];
    public ?int $department = null;
    public ?int $supervisor = null;
    public array $selectedRoles = [];

    public ?int $profession = null;
    public ?int $stage = null;
    public ?Carbon $joined_at = null;

    public ?string $status = null;
    public ?string $model_status = null;

    public bool $invitation = false;


    #[On('create-employee-modal')]
    public function openCreateEmployeeModal(): void
    {
        // $this->authorize('create', User::class);

        $this->resetFormInputs();

        // Setze Standardwerte
        $this->gender = Gender::Male->value;
        $this->selectedTeams = $this->currentTeamId ? [$this->currentTeamId] : [];
        $this->model_status = ModelStatus::ACTIVE->value;
        $this->status = EmployeeStatus::PROBATION->value;
        $this->invitation = true;
        $this->showCreateModal = true;

        // Lade nur was initial benötigt wird
        $this->loadRelationsData([
            'teams', 'departments', 'roles', 'professions', 'stages', 'supervisors'
        ]);
    }

    /**
     * Führt alle notwendigen DB-Operationen in einer Transaktion aus
     */
    public function saveEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {

                // Check ob ausgewählte Rollen eine Manager-Rolle enthalten
                $hasManagerRole = collect($this->roles)
                    ->whereIn('id', $this->selectedRoles)
                    ->contains('is_manager', true);

                $user = User::create([
                    'gender' => $this->gender,
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make(Str::password()),
                    'email_verified_at' => now(),
                    'department_id' => $this->department,
                    'supervisor_id' => $this->supervisor,
                    'profession_id' => $this->profession,
                    'stage_id' => $this->stage,
                    'joined_at' => $this->joined_at?->toDateString(),
                    'user_type' => UserType::Employee->value,
                    'model_status' => $this->model_status,
                    'status' => $this->status,
                    'company_id' => $this->companyId,
                    'created_by' => $this->authUserId,
                    'manager' => $hasManagerRole,
                ]);

                $this->createEmployee($user);
                $this->assignTeams($user);
                $this->assignRoles($user);

                // Cache clearen wenn Manager erstellt wurde
                if ($hasManagerRole) {
                    User::clearManagerCache($user->company_id);
                }


                if ($this->invitation) {
                    // TODO: E-Mail-Benachrichtigung implementieren
                }
            });


            $this->closeCreateEmployeeModal();
            $this->dispatch('employee-created');

            Flux::toast(
                text: __('Employee created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleSavingError($e);
        }
    }

    /**
     * Private Save Helper-Methoden
     */
    private function createEmployee(User $user): void
    {
        Employee::create([
            'user_id' => $user->id,
        ]);
    }

    private function assignTeams(User $user): void
    {
        if (!empty($this->selectedTeams)) {
            $teamsWithRole = collect($this->selectedTeams)
                ->mapWithKeys(fn($teamId) => [$teamId => ['role' => 'member']])
                ->toArray();

            $user->teams()->attach($teamsWithRole);

        } else {
            // Es braucht eine Jetstream Rolle
            $user->teams()->attach($this->currentTeamId, ['role' => 'member']);
        }
    }

    private function assignRoles(User $user): void
    {
        if (!empty($this->selectedRoles)) {
            $user->roles()->sync($this->selectedRoles);
        }
    }

    /**
     * Schließt das Modal und bereinigt alle Daten
     */
    public function closeCreateEmployeeModal(): void
    {
        $this->modal('create-employee')->close();

        $this->js("
        setTimeout(() => {
              \$wire.resetFormInputs();
            }, 1);
        ");

        $this->showCreateModal = false;
    }

    public function resetFormInputs(): void
    {
        $this->resetErrorBag();

        $this->reset([
            'gender', 'name', 'email', 'selectedTeams',
            'department', 'supervisor', 'selectedRoles', 'profession',
            'stage', 'joined_at', 'status', 'model_status',
            'invitation',
        ]);

        $this->resetDropdownRelationsData();
    }

    public function render(): View
    {
        return view('livewire.alem.employee.create');
    }

}
