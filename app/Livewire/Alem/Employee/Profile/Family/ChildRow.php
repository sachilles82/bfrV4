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

    public $child;

    public ChildForm $form;

    public function mount()
    {
        $this->form->setChild($this->child);
    }

    public function updateChild()
    {
        $this->form->update();

        $this->child->refresh();
    }

    public function render()
    {
        return view('livewire.alem.employee.profile.family.child-row');
    }
}
