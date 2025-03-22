<?php

namespace App\Traits\Modal;

use Illuminate\View\View;

trait WithPlaceholder
{

    public function placeholder(): View
    {
        return view('livewire.placeholders.modal.open-manager');
    }

}
