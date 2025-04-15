<?php

namespace App\Livewire\Alem\Employee;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DynamicNavigation extends Component
{
    public User $user;

    public string $activeTab;

    public function mount(User $user, string $activeTab = 'employee-update'): void
    {
        $this->user = $user;
        $this->activeTab = $activeTab;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.dynamic-navigation');
    }
}
