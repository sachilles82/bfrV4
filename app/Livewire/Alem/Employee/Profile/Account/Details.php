<?php

namespace App\Livewire\Alem\Employee\Profile\Account;

use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Livewire\Alem\Employee\Profile\Account\Helper\ValidateAccountDetails;
use App\Models\User;
use App\Traits\Enum\GenderOptions;
use App\Traits\Model\ModelStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy]
class Details extends Component
{
    use AuthUserTeamCompanyId, WithDropDownRelations;
    use AuthorizesRequests, ValidateAccountDetails;
    use ModelStatusOptions, GenderOptions;

    // NICHT public User $employee - das verursacht die doppelte Query!

    #[Locked]
    public int $employeeId;

    private User $employee; // Private property

    // User form fields
    public ?Gender $gender = null;
    public ?string $name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $phone_1 = null;
    public ?ModelStatus $model_status = null;
    public ?int $department = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    public function mount(
        $employee, // Nicht typisiert!
        int $authUserId,
        int $currentTeamId,
        int $companyId
    ): void {
        // Extrahiere nur die ID
        $this->employeeId = is_object($employee) ? $employee->id : $employee['id'];

        // Auth Daten
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Lade den Employee einmal mit allen benötigten Daten
        $this->loadEmployeeData();

        // Lade nur Dropdown-Daten
        $this->loadRelationsData(['teams', 'departments', 'roles']);
    }

    private function loadEmployeeData(): void
    {
        $this->employee = User::with([
            'teams:id,name',
            'roles:id,name,is_manager',
            'department:id,name'
        ])->findOrFail($this->employeeId);

        $this->populateFormFields();
    }

    private function populateFormFields(): void
    {
        $this->gender = $this->employee->gender;
        $this->name = $this->employee->name;
        $this->last_name = $this->employee->last_name;
        $this->email = $this->employee->email;
        $this->phone_1 = $this->employee->phone_1 ?? '';
        $this->model_status = $this->employee->model_status;
        $this->department = $this->employee->department_id;

        $this->selectedTeams = $this->employee->teams->pluck('id')->toArray();
        $this->selectedRoles = $this->employee->roles->pluck('id')->toArray();
    }

    #[On('employee-data-refreshed')]
    public function refreshFromParent(int $employeeId): void
    {
        if ($employeeId === $this->employeeId) {
            $this->loadEmployeeData();
        }
    }

    public function updateEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Lade fresh für Update
                $employee = User::findOrFail($this->employeeId);

                $employee->update([
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone_1' => $this->phone_1,
                    'gender' => $this->gender,
                    'model_status' => $this->model_status,
                    'department_id' => $this->department,
                ]);

                $this->syncRelations($employee);
            });

            // Benachrichtige andere Components
            $this->dispatch('employee-basic-data-updated', employeeId: $this->employeeId);

            Flux::toast(
                text: __('Employee Profile updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Error updating employee profile: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    private function syncRelations(User $employee): void
    {
        DB::transaction(function () use ($employee): void {
            $wasManager = $employee->hasManagerRole();

            $employee->roles()->sync($this->selectedRoles);
            $employee->teams()->sync($this->selectedTeams);

            if ($wasManager !== $employee->hasManagerRole()) {
                User::clearManagerCache($employee->company_id);
                $this->forceReloadCollection('supervisors');
            }
        });
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
