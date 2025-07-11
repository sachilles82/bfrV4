<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Livewire\Forms\ChildForm;
use App\Models\Alem\Child;
use App\Traits\Enum\GenderOptions;
use Flux\Flux;
use Livewire\Component;

class ChildRow extends Component
{
    use GenderOptions;

//    public Child $child;
//
//    // Form Properties
//    public $name;
//    public $gender;
//    public $birthdate;
//    public $ahv_number;
//    public $valid_until;
//
//    public function mount(): void
//    {
//        $this->fill([
//            'name' => $this->child->name,
//            'gender' => $this->child->gender?->value,
//            'birthdate' => $this->child->birthdate?->format('Y-m-d'),
//            'ahv_number' => $this->child->ahv_number,
//            'valid_until' => $this->child->valid_until?->format('Y-m-d'),
//        ]);
//    }
//
//    public function updateChild(): void
//    {
//        $validated = $this->validate([
//            'name' => 'required|string|max:255',
//            'gender' => 'nullable|string',
//            'birthdate' => 'nullable|date|before:today',
//            'ahv_number' => ['nullable', 'regex:/^\d{3}\.\d{4}\.\d{4}\.\d{2}$/'],
//            'valid_until' => 'nullable|date|after:today',
//        ]);
//
//        // Auto-calculate valid_until
//        if ($this->birthdate && !$this->valid_until) {
//            $birthdate = \Carbon\Carbon::parse($this->birthdate);
//            $this->valid_until = $birthdate->copy()->addYears(18)->format('Y-m-d');
//            $validated['valid_until'] = $this->valid_until;
//        }
//
//        $this->child->update($validated);
//
//        $this->dispatch('child-updated');
//        $this->modal('edit-child-' . $this->child->id)->close();
//
//        Flux::toast(
//            text: __('Child updated successfully.'),
//            heading: __('Success'),
//            variant: 'success'
//        );
//    }
//

    public $child;

    public ChildForm $form;

    public $showEditDialog = false;

    public function mount()
    {
        $this->form->setChild($this->child);
    }

    public function save()
    {
        $this->form->update();

        $this->child->refresh();

        $this->reset('showEditDialog');
    }
    public function render()
    {
        return view('livewire.alem.employee.profile.family.child-row');
    }
}
