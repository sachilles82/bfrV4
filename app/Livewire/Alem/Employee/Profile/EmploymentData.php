<?php

namespace App\Livewire\Alem\Employee\Profile;

use App\Enums\Employee\CivilStatus;
use App\Enums\Employee\Religion;
use App\Enums\Employee\Residence;
use App\Livewire\Alem\Employee\Profile\Helper\ValidateEmploymentData;
use App\Models\Address\Country;
use App\Models\Alem\Employee\Employee;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class EmploymentData extends Component
{
    use AuthorizesRequests, ValidateEmploymentData;

    public User $user;

    public ?string $ahv_number = '';

    public ?string $birthdate = '';

    public ?string $nationality = '';

    public ?string $hometown = '';

    public ?string $religion = '';

    public ?string $civil_status = '';

    public ?string $residence_permit = '';

    // Länder für das Dropdown
    public array $countries = [];

    /**
     * Der Mount-Hook erhält einen User.
     */
    public function mount(User $user): void
    {
        // Eager load employee relation to avoid N+1 issues
        $user->load('employee');

        $this->user = $user;

        // Länder dauerhaft cachen (rememberForever)
        $this->countries = Cache::rememberForever('countries-all', function () {
            return Country::select([
                'id', 'name', 'code',
            ])
                ->orderBy('id')
                ->get()
                ->toArray();
        });

        // Wenn der Mitarbeiter-Datensatz existiert, setze die lokalen Felder
        if ($user->employee) {
            $this->ahv_number = $user->employee->ahv_number ?? '';
            $this->birthdate = $user->employee->birthdate ? $user->employee->birthdate->format('Y-m-d') : '';
            $this->nationality = $user->employee->nationality ?? '';
            $this->hometown = $user->employee->hometown ?? '';
            $this->religion = $user->employee->religion ? $user->employee->religion->value : Religion::NoConfession->value;
            $this->civil_status = $user->employee->civil_status ? $user->employee->civil_status->value : CivilStatus::Single->value;
            $this->residence_permit = $user->employee->residence_permit ? $user->employee->residence_permit->value : Residence::C->value;
        } else {
            // Standardwerte setzen, wenn kein Mitarbeiter-Datensatz existiert
            $this->religion = Religion::NoConfession->value;
            $this->civil_status = CivilStatus::Single->value;
            $this->residence_permit = Residence::C->value;
        }
    }

    /**
     * Aktualisiert oder erstellt die Mitarbeiterdaten
     */
    public function updateEmployee(): void
    {
        // Validierung durchführen (aus dem Trait)
        $this->validate();

        try {
            // Convert string values to enum objects
            $religionEnum = Religion::from($this->religion);
            $civilStatusEnum = CivilStatus::from($this->civil_status);
            $residenceEnum = Residence::from($this->residence_permit);

            // Wenn der Mitarbeiter-Datensatz bereits existiert, aktualisiere ihn
            if ($this->user->employee) {
                $this->user->employee->update([
                    'ahv_number' => $this->ahv_number,
                    'birthdate' => $this->birthdate,
                    'nationality' => $this->nationality,
                    'hometown' => $this->hometown,
                    'religion' => $religionEnum,
                    'civil_status' => $civilStatusEnum,
                    'residence_permit' => $residenceEnum,
                ]);
            } else {
                // Erstelle einen neuen Mitarbeiter-Datensatz, wenn keiner existiert
                Employee::create([
                    'user_id' => $this->user->id,
                    'uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'ahv_number' => $this->ahv_number,
                    'birthdate' => $this->birthdate,
                    'nationality' => $this->nationality,
                    'hometown' => $this->hometown,
                    'religion' => $religionEnum,
                    'civil_status' => $civilStatusEnum,
                    'residence_permit' => $residenceEnum,
                ]);
            }

            // Erfolgsmeldung anzeigen
            Flux::toast(
                text: __('Employment data updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

            // Event auslösen, dass Mitarbeiterdaten aktualisiert wurden
            $this->dispatch('employment-data-updated');

        } catch (\Exception $e) {
            // Fehlermeldung anzeigen
            Flux::toast(
                text: __('Error updating employment data: ').$e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Rendert die View
     */
    public function render(): View
    {
        return view('livewire.alem.employee.profile.employment-data');
    }
}
