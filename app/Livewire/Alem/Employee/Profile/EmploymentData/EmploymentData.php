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

    // Employee identification
    public ?User $employeeUser = null;
    public ?Employee $employee = null;
    public int $employeeId;
    protected string $componentCacheKey;

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
        int $employeeId,
        int $authUserId,
        int $currentTeamId,
        int $companyId
    ): void {
        $this->employeeId = $employeeId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Component-spezifischer Cache-Key
        $this->componentCacheKey = "employee:{$employeeId}:employment-data";

        // Lade User mit Employee-Daten
        $this->loadEmploymentData();

        // Lade Länder für Dropdown
        $this->loadCountries();
    }

    private function loadEmploymentData(): void
    {
        // Nutzt automatisch den SELBEN Cache wenn im gleichen Request!
        $this->employeeUser = User::getForComponent(
            userId: $this->employeeId,
            relations: ['employee'],
//            select: ['id', 'name', 'last_name']
        );

        if ($this->employeeUser) {
            $this->employee = $this->employeeUser->employee;

            if ($this->employee) {
                $this->ahv_number = $this->employee->ahv_number ?? '';
                $this->birthdate = $this->employee->birthdate?->format('Y-m-d') ?? '';
                $this->nationality = $this->employee->nationality ?? '';
                $this->hometown = $this->employee->hometown ?? '';
                $this->religion = $this->employee->religion;
                $this->civil_status = $this->employee->civil_status;
                $this->residence_permit = $this->employee->residence_permit;
            }
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

    #[On('employee-basic-data-updated')]
    public function refreshIfNeeded(int $employeeId): void
    {
        if ($employeeId === $this->employeeId) {
            // Nur User-Name aktualisieren falls benötigt
            $this->employeeUser = User::select('id', 'name', 'last_name')->find($this->employeeId);
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

                if ($this->employee) {
                    $this->employee->update($employmentData);
                } else {
                    $this->employee = Employee::create([
                        'user_id' => $this->employeeUser->id,
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        ...$employmentData
                    ]);
                }
            });

            // Cache invalidieren
            Cache::forget($this->componentCacheKey);

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

    public function placeholder (): string
    {
        return view('livewire.placeholders.company.update');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.employment-data');
    }
}
