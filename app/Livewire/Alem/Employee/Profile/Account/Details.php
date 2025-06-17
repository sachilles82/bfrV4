<?php

namespace App\Livewire\Alem\Employee\Profile\Account;

use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Livewire\Alem\Employee\Profile\Account\Helper\ValidateAccountDetails;
use App\Models\User;
use App\Models\Alem\Employee;
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

#[Lazy(isolate: true)]
class Details extends Component
{
    use AuthUserTeamCompanyId, WithDropDownRelations;
    use AuthorizesRequests, ValidateAccountDetails;
    use ModelStatusOptions, GenderOptions;

    // WICHTIG: Verwende nur IDs, keine Models!
    #[Locked]
    public int $employeeId;

    // Transiente Properties (nicht von Livewire getrackt)
    protected ?User $employee = null;
    protected ?Employee $employeeModel = null;

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
        int $employeeId,
        int $authUserId,
        int $currentTeamId,
        int $companyId
    ): void {
        $this->employeeId = $employeeId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Lade initial data
        $this->loadEmployeeData();

        // Lade nur Dropdown-Daten
        $this->loadRelationsData(['teams', 'departments', 'roles']);
    }

    /**
     * Zentrale Methode zum Laden der Employee Daten
     */
    protected function loadEmployeeData(): void
    {
        $this->employee = User::getForComponent(
            userId: $this->employeeId,
            relations: [
                'teams:id,name',
                'roles:id,name,is_manager',
                'department:id,name'
            ],
            select: ['id', 'name', 'last_name', 'email', 'phone_1', 'gender', 'model_status', 'department_id']
        );

        if ($this->employee) {
            $this->populateFormFields();
        }
    }

    /**
     * Lade Employee Model nur wenn nötig
     */
    protected function getEmployee(): User
    {
        if (!$this->employee) {
            // Nutze Request-Cache
            $cacheKey = "request_employee_{$this->employeeId}";

            if (isset($GLOBALS[$cacheKey])) {
                $this->employee = $GLOBALS[$cacheKey];
            } else {
                $this->employee = User::with([
                    'teams:id,name',
                    'roles:id,name,is_manager',
                    'department:id,name'
                ])->find($this->employeeId);

                $GLOBALS[$cacheKey] = $this->employee;
            }
        }

        return $this->employee;
    }

    private function populateFormFields(): void
    {
        $employee = $this->getEmployee();

        $this->gender = $employee->gender;
        $this->name = $employee->name;
        $this->last_name = $employee->last_name;
        $this->email = $employee->email;
        $this->phone_1 = $employee->phone_1 ?? '';
        $this->model_status = $employee->model_status;
        $this->department = $employee->department_id;

        // Relations sollten bereits geladen sein
        if ($employee->relationLoaded('teams')) {
            $this->selectedTeams = $employee->teams->pluck('id')->toArray();
        }

        if ($employee->relationLoaded('roles')) {
            $this->selectedRoles = $employee->roles->pluck('id')->toArray();
        }
    }

    /**
     * Refresh wenn Parent neue Daten sendet
     */
    #[On('employee-data-refreshed')]
    public function refreshFromParent(int $employeeId): void
    {
        if ($employeeId === $this->employeeId) {
            // Clear transient data
            $this->employee = null;
            $this->employeeModel = null;

            // Clear Request-Cache
            unset($GLOBALS["request_employee_{$this->employeeId}"]);

            // Reload
            $this->populateFormFields();
        }
    }

    public function updateEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $employee = $this->getEmployee();

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

            // Benachrichtige Parent
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
        return view('livewire.alem.employee.profile.account.details');
    }
}
