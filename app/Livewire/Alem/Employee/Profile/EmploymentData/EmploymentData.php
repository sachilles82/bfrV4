<?php

namespace App\Livewire\Alem\Employee\Profile\EmploymentData;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use App\Livewire\Alem\Employee\Profile\EmploymentData\Helper\EmployeeDataEnums;
use App\Livewire\Alem\Employee\Profile\EmploymentData\Helper\ValidateEmploymentData;
use App\Models\Address\Country;
use App\Models\Alem\Employee;
use App\Models\User;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy(isolate: true)]
class EmploymentData extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId, ValidateEmploymentData, EmployeeDataEnums;

    // Empfange Daten vom Parent
    public User $employee;
    public ?Employee $employeeModel = null;
    public int $employeeId;

    // Employee form fields
    public ?string $ahv_number = '';
    public ?string $birthdate = '';
    public ?string $nationality = '';
    public ?string $hometown = '';
    public ?Religion $religion = null;
    public ?CivilStatus $civil_status = null;
    public ?Residence $residence_permit = null;

    // Dropdown data
    public array $countries = [];

    public function mount(
        User $employee,
        ?Employee $employeeModel,
        int $employeeId,
        int $authUserId,
        int $currentTeamId,
        int $companyId
    ): void {
        // Empfange Daten vom Parent
        $this->employee = $employee;
        $this->employeeModel = $employeeModel;
        $this->employeeId = $employeeId;

        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Populate form fields von übergebenen Daten
        $this->populateFormFields();

        // Lade nur Countries für Dropdown
        $this->loadCountries();
    }

    private function populateFormFields(): void
    {
        if ($this->employeeModel) {
            $this->ahv_number = $this->employeeModel->ahv_number ?? '';
            $this->birthdate = $this->employeeModel->birthdate?->format('Y-m-d') ?? '';
            $this->nationality = $this->employeeModel->nationality ?? '';
            $this->hometown = $this->employeeModel->hometown ?? '';
            $this->religion = $this->employeeModel->religion;
            $this->civil_status = $this->employeeModel->civil_status;
            $this->residence_permit = $this->employeeModel->residence_permit;
        }
    }

    private function loadCountries(): void
    {
        $this->countries = Cache::rememberForever('countries-dropdown', function () {
            return Country::select(['id', 'name', 'code'])
                ->orderBy('name')
                ->get()
                ->map(fn($country) => [
                    'id' => $country->id,
                    'name' => $country->name,
                    'code' => $country->code
                ])
                ->toArray();
        });
    }

    /**
     * Refresh Daten wenn Parent neue Daten hat
     */
    #[On('employee-data-refreshed')]
    public function refreshFromParent(User $employee): void
    {
        if ($employee->id === $this->employeeId) {
            $this->employee = $employee;
            $this->employeeModel = $employee->employee;
            $this->populateFormFields();
        }
    }

    public function updateEmploymentData(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $employmentData = [
                    'ahv_number' => $this->ahv_number,
                    'birthdate' => $this->birthdate ?: null,
                    'nationality' => $this->nationality,
                    'hometown' => $this->hometown,
                    'religion' => $this->religion,
                    'civil_status' => $this->civil_status,
                    'residence_permit' => $this->residence_permit,
                ];

                if ($this->employeeModel) {
                    $this->employeeModel->update($employmentData);
                } else {
                    // Erstelle neuen Employee Record
                    $this->employeeModel = Employee::create([
                        'user_id' => $this->employee->id,
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        ...$employmentData
                    ]);
                }
            });

            // Benachrichtige Parent zum Refresh
            $this->dispatch('employment-data-updated', employeeId: $this->employeeId);

            Flux::toast(
                text: __('Employment data updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            Flux::toast(
                text: __('Error updating employment data: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function placeholder(): string
    {
        return view('livewire.placeholders.company.update');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.employment-data');
    }
}
