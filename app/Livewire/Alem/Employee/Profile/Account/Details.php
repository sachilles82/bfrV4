<?php

namespace App\Livewire\Alem\Employee\Profile\Account;

use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Livewire\Alem\Employee\Profile\Account\Helper\ValidateAccountDetails;
use App\Models\User;
use App\Traits\Enum\GenderOptions;
use App\Traits\Livewire\ComponentDataLoader;
use App\Traits\Model\ModelStatusOptions;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy(isolate: true)]
class Details extends Component
{
    use AuthUserTeamCompanyId, WithDropDownRelations;
    use ComponentDataLoader; // NEU: Unser Trait für optimiertes Datenladen
    use AuthorizesRequests, ValidateAccountDetails;
    use ModelStatusOptions, GenderOptions;

    // Employee id holen
    public ?User $employee = null;
    public int $employeeId;

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

    public function mount(int $employeeId, int $authUserId, int $currentTeamId, int $companyId): void {
        $this->employeeId = $employeeId;

        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Nutze den Trait für optimiertes Laden
        $this->loadEmployeeData();

        // Lade nur benötigte Dropdown-Daten
        $this->loadRelationsData(['teams', 'departments', 'roles']);
    }

    private function loadEmployeeData(): void
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

    private function populateFormFields(): void
    {
        $this->gender = $this->employee->gender;
        $this->name = $this->employee->name;
        $this->last_name = $this->employee->last_name;
        $this->email = $this->employee->email;
        $this->phone_1 = $this->employee->phone_1 ?? '';
        $this->model_status = $this->employee->model_status;
        $this->department = $this->employee->department_id;

        // Relations
        $this->selectedTeams = $this->employee->teams->pluck('id')->toArray();
        $this->selectedRoles = $this->employee->roles->pluck('id')->toArray();
    }

    public function updateEmployee(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $this->employee->update([
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'phone_1' => $this->phone_1,
                    'gender' => $this->gender,
                    'model_status' => $this->model_status,
                    'department_id' => $this->department,
                ]);

                $this->syncRelations();
            });

            // Nutze Trait-Methode zum Cache invalidieren
            $this->invalidateComponentCache(User::class, $this->employeeId);
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

    private function syncRelations(): void
    {
        DB::transaction(function (): void {
            $wasManager = $this->employee->hasManagerRole();

            $this->employee->roles()->sync($this->selectedRoles);
            $this->employee->teams()->sync($this->selectedTeams);

            if ($wasManager !== $this->employee->hasManagerRole()) {
                User::clearManagerCache($this->employee->company_id);
                $this->forceReloadCollection('supervisors');
            }
        });
    }

    public function placeholder (): string
    {
        return view('livewire.placeholders.employee.details');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.account.details');
    }
}
