<?php

namespace App\Livewire\Account\Employee;

use App\Models\User;
use App\Models\Account\Employee;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use App\Livewire\Account\Employee\Helper\ValidateEmployee;

class EmployeeUpdate extends Component
{
    use AuthorizesRequests, ValidateEmployee;

    public Employee $employee;

    // Lokale Properties für User-Daten
    public string $employee_name;
    public string $email;
    public ?string $gender = '';

    // Lokale Properties für Employee-Daten
    public string $date_hired;
    public ?string $date_fired = null;
    public ?string $probation = null;
    public ?string $social_number = '';
    public ?string $personal_number = '';
    public ?string $profession = '';

    public function mount(User $user): void
    {
        // Wir laden den Employee-Datensatz über den User
        $user->loadMissing('employee');
        $this->employee = $user->employee;

        // Fülle die lokalen Felder mit den Werten aus User und Employee
        $this->employee_name = $user->name;
        $this->email         = $user->email;
        $this->gender        = $user->gender ?? '';

        $this->date_hired      = $this->employee->date_hired ? $this->employee->date_hired->format('Y-m-d') : '';
        $this->date_fired      = $this->employee->date_fired ? $this->employee->date_fired->format('Y-m-d') : null;
        $this->probation       = $this->employee->probation ? $this->employee->probation->format('Y-m-d') : null;
        $this->social_number   = $this->employee->social_number ?? '';
        $this->personal_number = $this->employee->personal_number ?? '';
        $this->profession      = $this->employee->profession ?? '';
    }

    public function updateEmployee(): void
    {
        $this->authorize('update', $this->employee);

        $this->validate();

        // Update der zugehörigen User-Daten
        $this->employee->user->update([
            'name'   => $this->employee_name,
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
        return view('livewire.account.employee.employee-update');
    }
}
