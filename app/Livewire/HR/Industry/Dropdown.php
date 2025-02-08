<?php

namespace App\Livewire\HR\Industry;

use App\Models\HR\Industry;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Dropdown extends Component
{
    public $industries;

    public function mount(): void
    {
        $this->industries = Industry::select('id','name')->orderBy('id')->get();
    }

    public function render(): View
    {
        return view('livewire.hr.industry.dropdown', [
            'industries' => $this->industries
        ]);
    }
}
