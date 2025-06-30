<?php

namespace App\Livewire\Alem\Employee\Profile\EmploymentData;

use App\Livewire\Alem\Employee\Profile\EmploymentData\Helper\EmployeeDataEnums;
use App\Livewire\Alem\Employee\Profile\EmploymentData\Helper\HandleCatchError;
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
use Livewire\Component;

#[Lazy]
class EmploymentData extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId;
    use ValidateEmploymentData, HandleCatchError;
    use EmployeeDataEnums;

    #[Locked]
    public int $userId;

    #[Locked]
    public ?Employee $employee = null;

    /** Employee form fields */
    public ?string $ahv_number = null;
    public ?string $nationality = null;
    public ?string $hometown = null;
    public ?string $religion = null;
    public ?string $civil_status = null;
    public ?string $residence_permit = null;

    /** Original Daten des Employees aus der Datenbank für Vergleiche */
    public array $originalData = [];

    /** Dropdown data */
    public array $countries = [];

    public function mount(int $userId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->userId = $userId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Lade Employee Model mit allen benötigten Feldern
        $this->employee = Employee::where('user_id', $this->userId)
            ->select([
                'id',
                'user_id',
                'ahv_number',
                'nationality',
                'hometown',
                'religion',
                'civil_status',
                'residence_permit'
            ])
            ->first();

        if ($this->employee) {
            $this->loadEmployeeData();
        }

        // Lade Countries für Dropdown
        $this->loadCountries();
    }

    /**
     * Befülle die Form mit Employee Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadEmployeeData(): void
    {
        if (!$this->employee) return;

        // WICHTIG: Speichere Original-Daten in EINEM public Array
        $this->originalData = [
            'ahv_number' => $this->employee->ahv_number,
            'nationality' => $this->employee->nationality,
            'hometown' => $this->employee->hometown,
            'religion' => $this->employee->religion?->value,
            'civil_status' => $this->employee->civil_status?->value,
            'residence_permit' => $this->employee->residence_permit?->value,
        ];

        // Setze Form-Felder
        $this->ahv_number = $this->employee->ahv_number ?? '';
        $this->nationality = $this->employee->nationality ?? '';
        $this->hometown = $this->employee->hometown ?? '';
        $this->religion = $this->employee->religion?->value;
        $this->civil_status = $this->employee->civil_status?->value;
        $this->residence_permit = $this->employee->residence_permit?->value;
    }

    /**
     * Lade Countries mit Cache
     */
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
     * Aktualisiert die Employment Daten in der Datenbank.
     * Validiert nur die geänderten Felder für bessere Performance
     */
    public function updateEmploymentData(): void
    {
        // Prüfe ob überhaupt Änderungen vorliegen
        if (!$this->hasAnyChanges()) {
            Flux::toast(
                text: __('No changes detected.'),
                heading: __('No Update'),
                variant: 'warning'
            );
            return;
        }

        // WICHTIG: Validiere NUR die geänderten Felder
        $this->validateOnlyChanged();

        try {
            DB::transaction(function () {
                // Erstelle Update-Array nur mit geänderten Feldern
                $updateData = [];

                if ($this->ahvNumberHasChanged()) {
                    $updateData['ahv_number'] = $this->ahv_number;
                }
                if ($this->nationalityHasChanged()) {
                    $updateData['nationality'] = $this->nationality;
                }
                if ($this->hometownHasChanged()) {
                    $updateData['hometown'] = $this->hometown;
                }
                if ($this->religionHasChanged()) {
                    $updateData['religion'] = $this->religion;
                }
                if ($this->civilStatusHasChanged()) {
                    $updateData['civil_status'] = $this->civil_status;
                }
                if ($this->residencePermitHasChanged()) {
                    $updateData['residence_permit'] = $this->residence_permit;
                }

                if ($this->employee) {
                    // Update nur wenn Felder geändert wurden
                    if (!empty($updateData)) {
                        $this->employee->update($updateData);
                    }
                } else {
                    // Erstelle neuen Employee Record
                    $this->employee = Employee::create([
                        'user_id' => $this->userId,
                        ...$updateData
                    ]);
                }
            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            $this->dispatch('employment-data-updated');

            Flux::toast(
                text: __('Employment data updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleEditingError($e);
        }
    }

    /**
     * Aktualisiert die Original-Daten nach erfolgreichem Speichern
     */
    private function updateOriginalDataAfterSave(): void
    {
        $this->originalData = [
            'ahv_number' => $this->ahv_number,
            'nationality' => $this->nationality,
            'hometown' => $this->hometown,
            'religion' => $this->religion,
            'civil_status' => $this->civil_status,
            'residence_permit' => $this->residence_permit,
        ];
    }

    public function placeholder(): string
    {
        return view('livewire.placeholders.employee.employment-data');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.employment-data', [
            'countries' => $this->countries
        ]);
    }
}
