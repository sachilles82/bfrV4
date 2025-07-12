<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Livewire\Alem\Employee\Profile\Family\Helper\HandleCatchError;
use App\Livewire\Alem\Employee\Profile\Family\Helper\ValidateChild;
use App\Models\Alem\Child;
use App\Traits\Enum\GenderOptions;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ChildRow extends Component
{
    use AuthorizesRequests;
    use ValidateChild, HandleCatchError;
    use GenderOptions;

    public Child $child;

    // Form fields
    public $name = '';
    public $gender = '';
    public $birthdate = '';
    public $ahv_number = '';
    public $valid_until = '';

    public function mount()
    {
        $this->initializeFormData();
    }

    protected function initializeFormData(): void
    {
        $this->name = $this->child->name;
        $this->gender = $this->child->gender?->value ?? '';
        $this->birthdate = $this->child->birthdate?->format('Y-m-d') ?? '';
        $this->ahv_number = $this->child->ahv_number ?? '';
        $this->valid_until = $this->child->valid_until?->format('Y-m-d') ?? '';
    }

    public function updateChild(): void
    {
        // Auto-calculate valid_until wenn birthdate gesetzt ist
        if ($this->birthdate && !$this->valid_until) {
            $birthdate = \Carbon\Carbon::parse($this->birthdate);
            $this->valid_until = $birthdate->copy()->addYears(18)->format('Y-m-d');
        }

        $this->child->update([
            'name' => $this->name,
            'gender' => $this->gender ?: null,
            'birthdate' => $this->birthdate ?: null,
            'ahv_number' => $this->ahv_number ?: null,
            'valid_until' => $this->valid_until ?: null,
        ]);

        $this->closeEditChildModal();

        $this->child->refresh();
    }

    /**
     * Schließt das Modal und bereinigt alle Daten
     */
    public function closeEditChildModal(): void
    {

        Flux::modals()->close();
//        $this->modal('edit-child')->close();

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

    public function render()
    {
        return view('livewire.alem.employee.profile.family.child-row');
    }
}
