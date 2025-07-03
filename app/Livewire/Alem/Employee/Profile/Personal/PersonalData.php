<?php

namespace App\Livewire\Alem\Employee\Profile\Personal;

use App\Livewire\Alem\Employee\Profile\Personal\Helper\EmployeeDataEnums;
use App\Livewire\Alem\Employee\Profile\Personal\Helper\ValidatePersonalData;
use App\Livewire\Alem\Employee\Profile\Personal\Helper\HandleCatchError;
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
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Lazy]
class PersonalData extends Component
{
    use AuthorizesRequests;
    use AuthUserTeamCompanyId;
    use ValidatePersonalData, HandleCatchError;
    use EmployeeDataEnums;

    #[Locked]
    public int $userId;

    public ?User $user = null;
    public ?Employee $employee = null;

    /** Employee form fields */
    public ?string $ahv_number = null;
    public ?string $residence_permit = null;
    public ?string $birthdate = null;
    public ?string $nationality = null;
    public ?string $hometown = null;
    public ?string $religion = null;

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

        // Lade Employee mit User Relation (BEST PRACTICE)
        $this->employee = Employee::with(['user:id,birthdate'])
            ->where('user_id', $this->userId)
            ->select([
                'id',
                'user_id',
                'ahv_number',
                'nationality',
                'hometown',
                'religion',
                'residence_permit'
            ])
            ->first();

        // Falls kein Employee existiert, lade nur den User
        if (!$this->employee) {
            $this->user = User::select(['id', 'birthdate'])
                ->findOrFail($this->userId);
        } else {
            // User ist bereits über die Relation geladen
            $this->user = $this->employee->user;
        }

        $this->loadPersonalData();
        $this->loadCountries();
    }

    /**
     * Befülle die Form mit Personal Daten
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadPersonalData(): void
    {
        // WICHTIG: Speichere Original-Daten in EINEM public Array
        // Formatiere Dates konsistent als String
        $this->originalData = [
            'birthdate' => $this->user?->birthdate?->format('Y-m-d'),
            'ahv_number' => $this->employee?->ahv_number,
            'nationality' => $this->employee?->nationality,
            'hometown' => $this->employee?->hometown,
            'religion' => $this->employee?->religion?->value,
            'residence_permit' => $this->employee?->residence_permit?->value,
        ];

        // Setze Form-Felder
        $this->birthdate = $this->user?->birthdate?->format('Y-m-d') ?? '';
        $this->ahv_number = $this->employee?->ahv_number ?? '';
        $this->nationality = $this->employee?->nationality ?? '';
        $this->hometown = $this->employee?->hometown ?? '';
        $this->religion = $this->employee?->religion?->value;
        $this->residence_permit = $this->employee?->residence_permit?->value;
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
     * Aktualisiert die Personal Daten in der Datenbank.
     * Validiert nur die geänderten Felder für bessere Performance
     */
    public function updatePersonalData(): void
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
                // Update User birthdate wenn geändert
                if ($this->birthdateHasChanged()) {
                    // Parse das Datum für die Datenbank
                    $parsedDate = !empty($this->birthdate) ? \Carbon\Carbon::parse($this->birthdate)->format('Y-m-d') : null;
                    $this->user->update(['birthdate' => $parsedDate]);
                }

                // Erstelle Update-Array nur mit geänderten Employee Feldern
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
                if ($this->residencePermitHasChanged()) {
                    $updateData['residence_permit'] = $this->residence_permit;
                }

                if ($this->employee) {
                    // Update nur wenn Felder geändert wurden
                    if (!empty($updateData)) {
                        $this->employee->update($updateData);
                    }
                } else {
                    // Erstelle neuen Employee Record nur wenn es Employee-Daten gibt
                    if (!empty($updateData)) {
                        $this->employee = Employee::create([
                            'user_id' => $this->userId,
                            ...$updateData
                        ]);
                    }
                }
            });

            // Aktualisiere Original-Daten nach erfolgreichem Update
            $this->updateOriginalDataAfterSave();

            $this->dispatch('personal-data-updated');

            Flux::toast(
                text: __('Personal data updated successfully.'),
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
            'birthdate' => $this->birthdate,
            'ahv_number' => $this->ahv_number,
            'nationality' => $this->nationality,
            'hometown' => $this->hometown,
            'religion' => $this->religion,
            'residence_permit' => $this->residence_permit,
        ];
    }

    public function placeholder(): string
    {
        return view('livewire.placeholders.employee.personal-data');
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.personal.personal-data', [
            'countries' => $this->countries
        ]);
    }
}
