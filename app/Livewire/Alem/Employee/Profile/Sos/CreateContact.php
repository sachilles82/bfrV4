<?php

namespace App\Livewire\Alem\Employee\Profile\Sos;

use App\Livewire\Alem\Employee\Profile\Sos\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Sos\Helper\ValidateContact;
use App\Models\Alem\SOS;
use App\Traits\Enum\GenderOptions;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CreateContact extends Component
{
    use AuthorizesRequests;
    use ValidateContact, HandleCatchError;
    use GenderOptions;

    // Parent User ID
    #[Locked]
    public int $userId;

    // Contact Form Fields
    public ?string $name = null;
    public ?string $gender = null;
    public ?string $related = null;
    public ?string $phone = null;
    public ?string $email = null;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Speichert einen neuen SOS Kontakt
     */
    public function add(): void
    {
        $this->validate();

        try {
            DB::transaction(function () {
                SOS::create([
                    'user_id' => $this->userId,
                    'name' => $this->name,
                    'gender' => $this->gender,
                    'related' => $this->related,
                    'phone' => $this->sanitizePhoneNumber($this->phone),
                    'email' => $this->email,
                ]);
            });

            $this->dispatch('added');

            $this->closeCreateContactModal();

            Flux::toast(
                text: __('Emergency contact added successfully.'),
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
    public function closeCreateContactModal(): void
    {
        Flux::modals()->close();

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
            'name', 'gender', 'related',
            'phone', 'email'
        ]);
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.sos.create-contact');
    }
}
