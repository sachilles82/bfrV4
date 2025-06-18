<?php

namespace App\Livewire\Alem\Employee\Profile\EmploymentData;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use App\Livewire\Alem\Employee\Profile\EmploymentData\Helper\EmployeeDataEnums;
use App\Livewire\Alem\Employee\Profile\EmploymentData\Helper\ValidateEmploymentData;
use App\Models\Address\Country;
use App\Models\Alem\Employee;
use App\Traits\User\AuthUserTeamCompanyId;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy(isolate: true)]
class EmploymentData extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId, ValidateEmploymentData, EmployeeDataEnums;

    // Nur user_id empfangen
    #[Locked]
    public int $userId;

    // Employee Model (wird in mount geladen)
    public ?Employee $employeeModel = null;

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

    public function mount(int $userId): void
    {
        $this->userId = $userId;

        // Auth Daten bei Bedarf laden (Trait nutzt auth()->user())
        $this->initializeAuthData();

        // Lade Employee Model
        $this->loadEmployeeData();

        // Lade Countries für Dropdown
        $this->loadCountries();
    }

    /**
     * Initialisiere Auth Daten vom Trait
     */
    private function initializeAuthData(): void
    {
        $authUser = auth()->user();
        if ($authUser) {
            $this->authUserId = $authUser->id;
            $this->currentTeamId = $authUser->current_team_id;
            $this->companyId = $authUser->company_id;
        }
    }

    /**
     * Lade Employee Daten
     */
    private function loadEmployeeData(): void
    {
        $this->employeeModel = Employee::where('user_id', $this->userId)
            ->select([
                'id',
                'user_id',
                'ahv_number',
                'nationality',
                'hometown',
                'birthdate',
                'religion',
                'civil_status',
                'residence_permit'
            ])
            ->first();

        if ($this->employeeModel) {
            $this->populateFormFields();
        }
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
     * Refresh Daten wenn Updates passieren
     */
    #[On('employee-data-refreshed')]
    public function refreshFromParent(int $employeeId): void
    {
        // Prüfe ob es der richtige Employee ist
        if ($this->employeeModel && $this->employeeModel->user_id === $employeeId) {
            $this->loadEmployeeData();
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
                        'user_id' => $this->userId,
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        ...$employmentData
                    ]);
                }
            });

            // Benachrichtige andere Components
            $this->dispatch('employment-data-updated', employeeId: $this->userId);

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
