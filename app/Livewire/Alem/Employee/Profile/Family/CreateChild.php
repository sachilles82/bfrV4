<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Livewire\Alem\Employee\Profile\Family\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Family\Helper\ValidateChild;
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
    use ValidateChild, HandleCatchError;
    use GenderOptions;

    // Parent User ID
    #[Locked]
    public int $userId;

    // Child Form Fields
    public ?string $name = null;
    public ?string $gender = null;
    public ?string $birthdate = null;
    public ?string $ahv_number = null;
    public ?string $valid_until = null;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Speichert ein neues Kind
     */
    public function add(): void
    {
        try {
            DB::transaction(function () {
                // Berechne valid_until wenn birthdate gesetzt ist
                if ($this->birthdate && !$this->valid_until) {
                    $birthdate = Carbon::parse($this->birthdate);
                    $this->valid_until = $birthdate->copy()->addYears(18)->format('Y-m-d');
                }

                Child::create([
                    'user_id' => $this->userId,
                    'name' => $this->name,
                    'gender' => $this->gender,
                    'birthdate' => $this->birthdate,
                    'ahv_number' => $this->ahv_number,
                    'valid_until' => $this->valid_until,
                ]);
            });

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
        $this->reset([
            'name', 'gender', 'birthdate',
            'ahv_number', 'valid_until'
        ]);
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.family.create-child');
    }
}
