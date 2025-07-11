<?php

namespace App\Livewire\Alem\Employee\Profile\Family;

use App\Livewire\Forms\ChildForm;
use App\Traits\Enum\GenderOptions;
use Livewire\Component;

class AddChildDialog extends Component
{

    use GenderOptions;
    public ChildForm $form;
    public $show = false;
    public int $userId;

    public function mount(int $userId): void
    {
        $this->userId = $userId;
        // Setze userId in der Form
        $this->form->setUserId($userId);
    }

    public function add(): void
    {
        $this->form->save();
        $this->reset('show');
        $this->dispatch('added');
    }

    public function render()
    {
        return view('livewire.alem.employee.profile.family.add-child-dialog');
    }
}
