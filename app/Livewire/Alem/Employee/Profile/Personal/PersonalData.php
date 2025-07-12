<?php

namespace App\Livewire\Alem\Employee\Profile\Personal;

use App\Livewire\Alem\Employee\Profile\Personal\Helper\ValidatePersonalData;
use App\Livewire\Alem\Employee\Profile\Personal\Helper\HandleCatchError;
use App\Models\Address\Country;
use App\Models\Alem\Employee;
use App\Models\User;
use App\Traits\Employee\EmployeeDataEnums;
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
    use ValidatePersonalData, HandleCatchError;
    use EmployeeDataEnums;

    #[Locked]
    public int $userId;

    /** Form fields */
    public ?string $birthdate = null;
    public ?string $ahv_number = null;
    public ?int $country_id = null;
    public ?string $hometown = null;
    public ?string $religion = null;
    public ?string $residence_permit = null;

    /** Original Daten für Vergleiche */
    public array $originalData = [];

    public function mount(int $userId): void
    {
        $this->userId = $userId;

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
     * Speichere Original-Daten aus der Datenbank für den Vergleich
     */
    private function loadPersonalData(): void
    {
        $user = $this->user;
        $employee = $this->employee;

        // Original-Daten speichern - KEINE empty strings, nur null
        $this->originalData = [
            'birthdate' => $user?->birthdate?->format('Y-m-d'),
            'ahv_number' => $employee?->ahv_number,
            'country_id' => $employee?->country_id,
            'hometown' => $employee?->hometown,
            'religion' => $employee?->religion?->value,
            'residence_permit' => $employee?->residence_permit?->value,
        ];

        // Setze Form-Felder NUR mit den originalData
        $this->birthdate = $this->originalData['birthdate'];
        $this->ahv_number = $this->originalData['ahv_number'];
        $this->country_id = $this->originalData['country_id'];
        $this->hometown = $this->originalData['hometown'];
        $this->religion = $this->originalData['religion'];
        $this->residence_permit = $this->originalData['residence_permit'];
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
                // Update User Felder wenn geändert
                $userUpdateData = [];

                if ($this->birthdateHasChanged()) {
                    $userUpdateData['birthdate'] = $this->birthdate ?: null;
                }

                // Update User nur wenn Felder geändert wurden
                if (!empty($userUpdateData)) {
                    $this->user->update($userUpdateData);
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
