<?php

namespace App\Livewire\Alem\Employee\Profile;

use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy(isolate: false)]
class EmergencyContact extends Component
{
    public function render()
    {
        return view('livewire.alem.employee.profile.emergency-contact');
    }
}
