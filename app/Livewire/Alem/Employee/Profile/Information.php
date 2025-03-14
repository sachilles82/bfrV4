<?php

namespace App\Livewire\Alem\Employee\Profile;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Employee\Profile\Helper\ValidateInformation;
use App\Models\Alem\Employee\Employee;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Information extends Component
{
    use ValidateInformation, AuthorizesRequests;

    // Der Employee-Datensatz, der über die Relation vom User geladen wird
    public Employee $employee;

    // Lokale Properties für User-Daten (vom zugehörigen User)
    public string $name;
    public string $last_name;
    public string $email;
    public ?string $gender = '';
    public ?string $phone_1 = '';
    public $model_status = '';

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
        $this->email = $user->email;
        $this->gender = $user->gender ?? '';
        $this->phone_1 = $user->phone_1 ?? ''; // Assuming phone maps to phone_1
        $this->model_status = $user->model_status ?? ModelStatus::ACTIVE->value;
    }

    /**
     * Hilfsmethode, um alle ModelStatus-Optionen für das Dropdown zu erhalten
     */
    public function getModelStatusOptionsProperty()
    {
        return collect(ModelStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
                'dotColor' => $status->dotColor(),
                'icon' => $status->icon()
            ];
        });
    }


    /**
     * Aktualisiert die Daten des zugehörigen User und Employee.
     */
    public function updateEmployee(): void
    {
        $this->authorize('update', $this->employee);

        $this->validate();

        try {
            // Update der User-Daten
            $this->employee->user->update([
                'gender' => $this->gender,
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone_1' => $this->phone_1, // Assuming phone maps to phone_1
                'model_status' => $this->model_status,
            ]);

            Flux::toast(
                text: __('Employee updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('update-table');

        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error updating employee: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.information');
    }
}
