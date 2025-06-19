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
use Throwable;

#[Lazy]
class Details extends Component
{
    use AuthUserTeamCompanyId, WithDropDownRelations;
    use AuthorizesRequests, ValidateAccountDetails;
    use ModelStatusOptions, GenderOptions;

    #[Locked]
    public int $employeeId;

    private User $employee;

    // User form fields
    public ?Gender $gender = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone_1 = null;
    public ?ModelStatus $model_status = null;
    public ?int $department = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    public function mount($employee, int $authUserId, int $currentTeamId, int $companyId): void {

        $this->employeeId = is_object($employee) ? $employee->id : $employee['id'];

        // Auth Daten
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        $this->employee = User::with([
            'teams:id,name',
            'roles:id,name,is_manager',
            'department:id,name'
        ])->findOrFail($this->employeeId);

        // Lade den Employee einmal mit allen benötigten Daten
        $this->loadEmployeeData();

        // Lade nur Dropdown-Daten
        $this->loadRelationsData([
            'teams', 'departments', 'roles', 'supervisors'
        ]);
    }

    /**
     * Lade die User Employee Daten
     */
    private function loadEmployeeData(): void
    {
        if (!$this->employee) return;

        $this->gender = $this->employee->gender;
        $this->name = $this->employee->name;
        $this->email = $this->employee->email;
        $this->phone_1 = $this->employee->phone_1 ?? '';

        $this->selectedTeams = $this->employee->teams->pluck('id')->toArray();
        $this->department = $this->employee->department_id;
        $this->selectedRoles = $this->employee->roles->pluck('id')->toArray();
        $this->model_status = $this->employee->model_status;
    }

    public function updateEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                // Lade fresh für Update
                $employee = User::findOrFail($this->employeeId);

                $employee->update([
                    'gender' => $this->gender,
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_1' => $this->phone_1,

                    'department_id' => $this->department,
                    'model_status' => $this->model_status,
                ]);

                $this->syncRelations($employee);

            });

            $this->dispatch('employee-updated');

            Flux::toast(
                text: __('Employee Account Details updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (Throwable $e) {
//            $this->handleUpdateEmployeeAccountDetails($e);
        }
    }

    /**
     * @throws Throwable
     */
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
