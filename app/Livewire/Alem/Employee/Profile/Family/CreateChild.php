<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Profile\Family\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Family\Helper\ValidateCreateChild;
use App\Models\Alem\Child;
use App\Traits\Enum\GenderOptions;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CreateChild extends Component
{
    use AuthorizesRequests;
    use ValidateCreateChild, HandleCatchError;
    use GenderOptions;

    // Parent User ID
    #[Locked]
    public int $userId;

    // Child Form Fields
    public ?string $name = null;
    public ?string $gender = null;
    public ?string $birthdate = null;
    public ?string $ahv_number = null;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
        $this->gender = Gender::Male->value;
    }

    /**
     * Speichert ein neues Kind
     */
    public function add(): void
    {
        try {
            // Validiere required Felder und gefüllte nullable Felder
            $this->validateRequiredAndFilled();

            DB::transaction(function () {
                // Bereite alle Daten einmal vor - vermeidet mehrfache Property-Zugriffe
                $birthdate = Carbon::parse($this->birthdate);

                $data = [
                    'user_id' => $this->userId,
                    'name' => $this->name,
                    'gender' => $this->gender,
                    'birthdate' => $this->birthdate,
                    'valid_until' => $birthdate->copy()->addYears(18)->format('Y-m-d'),
                ];

                // Füge nullable Felder nur hinzu wenn gefüllt
                if (filled($this->ahv_number)) {
                    $data['ahv_number'] = $this->ahv_number;
                }

                // Erstelle Child
                Child::create($data);
            });

            // Dispatch events und UI updates außerhalb der Transaktion
            $this->dispatch('added');
            $this->closeCreateChildModal();

            Flux::toast(
                text: __('Child added successfully.'),
                heading: __('Success'),
                variant: 'success'
            );

        } catch (\Throwable $e) {
            $this->handleSavingError($e);
        }
    }

    /**
     * Schließt das Modal und bereinigt alle Daten
     */
    public function closeCreateChildModal(): void
    {
        $this->modal('create-child')->close();

        $this->js("
            setTimeout(() => {
                \$wire.resetFormInputs();
            }, 1);
        ");
    }

    public function resetFormInputs(): void
    {
        $this->resetErrorBag();
        $this->reset(['name', 'birthdate', 'ahv_number']);
        // Gender direkt setzen statt reset + neu setzen
        $this->gender = Gender::Male->value;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.family.create-child');
    }
}
