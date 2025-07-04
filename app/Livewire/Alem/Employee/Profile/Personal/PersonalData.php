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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
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

    /** Employee form fields */
    public ?string $ahv_number = null;
    public ?string $residence_permit = null;
    public ?string $birthdate = null;
    public ?int $country_id = null;
    public ?string $hometown = null;
    public ?string $religion = null;

    /** Original Daten des Employees aus der Datenbank für Vergleiche */
    public array $originalData = [];

    public function mount(int $userId, int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->userId = $userId;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        $this->loadPersonalData();
    }

    /**
     * Employee als Computed Property
     * Wird automatisch gecached und nach Validation Error neu geladen
     */
    #[Computed]
    public function employee(): ?Employee
    {
        return Employee::with([
            'user:id,birthdate',
            'country:id,name,code'
        ])
            ->where('user_id', $this->userId)
            ->select([
                'id',
                'user_id',
                'ahv_number',
                'country_id',
                'hometown',
                'religion',
                'residence_permit'
            ])
            ->first();
    }

    /**
     * User als Computed Property
     * Lädt User entweder über Employee Relation oder direkt
     */
    #[Computed]
    public function user(): User
    {
        // Wenn Employee existiert, nutze die Relation
        if ($this->employee && $this->employee->relationLoaded('user')) {
            return $this->employee->user;
        }

        // Sonst lade User direkt
        return User::select(['id', 'birthdate'])
            ->findOrFail($this->userId);
    }

    /**
     * Countries als Computed Property
     */
    #[Computed(cache: true)]
    public function countries(): Collection
    {
        return Country::getCountriesDropdown();
    }

    /**
     * Befülle die Form mit Personal Daten
     */
    private function loadPersonalData(): void
    {
        // Nutze Computed Properties - werden automatisch geladen
        $user = $this->user;
        $employee = $this->employee;

        $userBirthdate = $user?->birthdate?->format('Y-m-d');

        $this->originalData = [
            'birthdate' => $userBirthdate,
            'ahv_number' => $employee?->ahv_number,
            'country_id' => $employee?->country_id,
            'hometown' => $employee?->hometown,
            'religion' => $employee?->religion?->value,
            'residence_permit' => $employee?->residence_permit?->value,
        ];

        // Setze Form-Felder
        $this->birthdate = $userBirthdate ?? '';
        $this->ahv_number = $employee?->ahv_number ?? '';
        $this->country_id = $employee?->country_id;
        $this->hometown = $employee?->hometown ?? '';
        $this->religion = $employee?->religion?->value;
        $this->residence_permit = $employee?->residence_permit?->value;
    }

    /**
     * Aktualisiert die Personal Daten in der Datenbank
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

        // Validiere NUR die geänderten Felder
        $this->validateOnlyChanged();

        try {
            DB::transaction(function () {
                // Update User birthdate wenn geändert
                // Computed Property lädt User automatisch falls nötig
                if ($this->birthdateHasChanged()) {
                    $parsedDate = !empty($this->birthdate)
                        ? \Carbon\Carbon::parse($this->birthdate)->format('Y-m-d')
                        : null;

                    $this->user->update(['birthdate' => $parsedDate]);
                }

                // Erstelle Update-Array nur mit geänderten Employee Feldern
                $updateData = [];

                if ($this->ahvNumberHasChanged()) {
                    $updateData['ahv_number'] = $this->ahv_number;
                }
                if ($this->countryIdHasChanged()) {
                    $updateData['country_id'] = $this->country_id;
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

                // Nutze Computed Property für Employee
                if ($this->employee) {
                    // Update nur wenn Felder geändert wurden
                    if (!empty($updateData)) {
                        $this->employee->update($updateData);
                    }
                } else {
                    // Erstelle neuen Employee Record nur wenn es Employee-Daten gibt
                    if (!empty($updateData)) {
                        Employee::create([
                            'user_id' => $this->userId,
                            ...$updateData
                        ]);

                        // WICHTIG: Clear Computed Property Cache nach Create
                        unset($this->employee);
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
            'country_id' => $this->country_id,
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
        return view('livewire.alem.employee.profile.personal.personal-data');
    }
}
