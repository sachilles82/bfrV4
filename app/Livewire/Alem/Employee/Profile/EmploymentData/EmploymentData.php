<?php

namespace App\Livewire\Alem\Employee\Profile\EmploymentData;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use App\Livewire\Alem\Employee\Helper\WithDropDownRelations;
use App\Livewire\Alem\Employee\Profile\Helper\ValidateEmploymentData;
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
use Livewire\Component;

#[Lazy(isolate: true)]
class EmploymentData extends Component
{
    use AuthorizesRequests, ValidateEmploymentData;
    use AuthUserTeamCompanyId, WithDropDownRelations;

    // User identification
    public User $user;
    public ?Employee $employee = null;
    protected string $sharedDataKey;

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

    // Static method für Relations
    public static function requiredRelations(): array
    {
        return ['employee'];
    }

    public function mount(
        string $sharedDataKey,
        int $authUserId,
        int $currentTeamId,
        int $companyId
    ): void {
        $this->sharedDataKey = $sharedDataKey;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Lade User aus Cache
        $this->loadUserFromCache();

        // Lade Länder für Dropdown
        $this->loadCountries();
    }

    private function loadUserFromCache(): void
    {
        $this->user = Cache::get($this->sharedDataKey);

        if ($this->user) {
            $this->employee = $this->user->employee;

            if ($this->employee) {
                $this->ahv_number = $this->employee->ahv_number ?? '';
                $this->birthdate = $this->employee->birthdate?->format('Y-m-d') ?? '';
                $this->nationality = $this->employee->nationality ?? '';
                $this->hometown = $this->employee->hometown ?? '';
                $this->religion = $this->employee->religion ?? Religion::NoConfession;
                $this->civil_status = $this->employee->civil_status ?? CivilStatus::Single;
                $this->residence_permit = $this->employee->residence_permit ?? Residence::C;
            } else {
                // Standardwerte für neue Employees
                $this->religion = Religion::NoConfession;
                $this->civil_status = CivilStatus::Single;
                $this->residence_permit = Residence::C;
            }
        }
    }

    private function loadCountries(): void
    {
        $this->countries = Cache::rememberForever('countries-all', function () {
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

    protected function shouldCheckModalState(): bool
    {
        return false;
    }

    protected function isModalOpen(): bool
    {
        return true;
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
                        'user_id' => $this->user->id,
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        ...$employmentData
                    ]);
                }
            });

            // Cache invalidieren
            Cache::forget($this->sharedDataKey);
            Cache::forget("employee_employment_data_{$this->user->id}");

            $this->dispatch('employment-data-updated');

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

    public function render(): View
    {
        return view('livewire.alem.employee.profile.employment-data');
    }
}
