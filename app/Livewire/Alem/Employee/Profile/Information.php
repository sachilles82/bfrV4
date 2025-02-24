<?php

namespace App\Livewire\Alem\Employee\Profile;

use App\Models\Alem\Employee\Employee;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Information extends Component
{
    use AuthorizesRequests;

    // Der Employee-Datensatz, der über die Relation vom User geladen wird
    public Employee $employee;

    // Lokale Properties für User-Daten (vom zugehörigen User)
    public string $name;
    public string $last_name;
    public string $email;
    public ?string $gender = '';

    // Lokale Properties für Employee-Daten
    public string $date_hired;
    public ?string $date_fired = null;
    public ?string $probation = null;
    public ?string $social_number = '';
    public ?string $personal_number = '';
    public ?string $profession = '';

    /**
     * Der Mount-Hook erhält einen User. Wir laden über dessen Employee-Relation den Employee-Datensatz.
     */
    public function mount(User $user): void
    {
        // Lade die Employee-Relation, falls nicht bereits geladen
        $user->loadMissing('employee');

        // Falls kein Employee-Datensatz existiert, breche mit 404 ab
        if (!$user->employee) {
            abort(404, __('Employee record not found for this user.'));
        }

        $this->employee = $user->employee;

        // Setze die lokalen Felder aus den User-Daten und Employee-Daten
        $this->name = $user->name;
        $this->last_name = $user->last_name;
        $this->email         = $user->email;
        $this->gender        = $user->gender ?? '';

        $this->date_hired      = $this->employee->date_hired ? $this->employee->date_hired->format('Y-m-d') : '';
        $this->date_fired      = $this->employee->date_fired ? $this->employee->date_fired->format('Y-m-d') : null;
        $this->probation       = $this->employee->probation ? $this->employee->probation->format('Y-m-d') : null;
        $this->social_number   = $this->employee->social_number ?? '';
        $this->personal_number = $this->employee->personal_number ?? '';
        $this->profession      = $this->employee->profession ?? '';
    }

    /**
     * Validierungsregeln für das Update-Formular.
     */
    protected function rules(): array
    {
        return [
            'name'   => 'required|string|min:3',
            'last_name'   => 'required|string|min:3',
            'email'           => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->employee->user->id),
            ],
            'gender'          => 'required|string',
            'date_hired'      => 'required|date',
            'date_fired'      => 'nullable|date',
            'probation'       => 'nullable|date',
            'social_number'   => 'nullable|string',
            'personal_number' => 'nullable|string',
            'profession'      => 'nullable|string',
        ];
    }

    /**
     * Aktualisiert die Daten des zugehörigen User und Employee.
     */
    public function updateEmployee(): void
    {
        $this->authorize('update', $this->employee);

        $this->validate();

        // Update der User-Daten
        $this->employee->user->update([
            'name'   => $this->name,
            'last_name'   => $this->last_name,
            'email'  => $this->email,
            'gender' => $this->gender,
        ]);

        // Update der Employee-Daten
        $this->employee->update([
            'date_hired'      => $this->date_hired,
            'date_fired'      => $this->date_fired,
            'probation'       => $this->probation,
            'social_number'   => $this->social_number,
            'personal_number' => $this->personal_number,
            'profession'      => $this->profession,
        ]);

        Flux::toast(
            text: __('Employee updated successfully.'),
            heading: __('Success.'),
            variant: 'success'
        );
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.information');
    }
}
