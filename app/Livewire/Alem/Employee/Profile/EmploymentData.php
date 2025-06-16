<?php

namespace App\Livewire\Alem\Employee\Profile;

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

    // Flag ob Daten geladen wurden
    private bool $dataLoaded = false;

    /**
     * Mount erhält den minimalen User und die Auth-Properties
     */
    public function mount(User $user, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->user = $user;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Sofort Employee-Daten laden
        $this->loadEmploymentData();

        // Lade Länder für Dropdown
        $this->loadCountries();
    }

    /**
     * Lädt alle benötigten Employment-Daten
     */
    private function loadEmploymentData(): void
    {
        if ($this->dataLoaded) {
            return;
        }

        // Lade User mit Employee Relation
        $this->user = User::with(['employee'])
            ->where('id', $this->user->id)
            ->first();

        $this->employee = $this->user->employee;

        if ($this->employee) {
            // Befülle die Komponenten-Properties
            $this->ahv_number = $this->employee->ahv_number ?? '';
            $this->birthdate = $this->employee->birthdate?->format('Y-m-d') ?? '';
            $this->nationality = $this->employee->nationality ?? '';
            $this->hometown = $this->employee->hometown ?? '';
            $this->religion = $this->employee->religion ?? Religion::NoConfession;
            $this->civil_status = $this->employee->civil_status ?? CivilStatus::Single;
            $this->residence_permit = $this->employee->residence_permit ?? Residence::C;
        } else {
            // Setze Standardwerte für neue Employees
            $this->religion = Religion::NoConfession;
            $this->civil_status = CivilStatus::Single;
            $this->residence_permit = Residence::C;
        }

        $this->dataLoaded = true;
    }

    /**
     * Lädt Länder-Daten für Dropdown
     */
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

    /**
     * Override der shouldCheckModalState für den WithDropDownRelations Trait
     */
    protected function shouldCheckModalState(): bool
    {
        return false;
    }

    protected function isModalOpen(): bool
    {
        return true;
    }

    /**
     * Aktualisiert die Employment-Daten
     */
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
                    // Update existing employee record
                    $this->employee->update($employmentData);
                } else {
                    // Create new employee record
                    $this->employee = Employee::create([
                        'user_id' => $this->user->id,
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        ...$employmentData
                    ]);

                    // Reload user with new employee relation
                    $this->user->load('employee');
                }
            });

            // Cache invalidieren
            $this->invalidateCaches();

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

    /**
     * Invalidiert relevante Caches
     */
    private function invalidateCaches(): void
    {
        Cache::forget("employee_employment_data_{$this->user->id}");
        Cache::forget("employee_profile_{$this->user->slug}_employment-data");
    }

//    /**
//     * Placeholder während des Ladens
//     */
//    public function placeholder(): View
//    {
//        return view('livewire.placeholders.form-skeleton');
//    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.employment-data');
    }
}
