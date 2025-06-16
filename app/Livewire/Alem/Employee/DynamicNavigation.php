<?php

namespace App\Livewire\Alem\Employee;

use App\Models\User;
use App\Traits\User\AuthUserTeamCompanyId;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DynamicNavigation extends Component
{
    use AuthUserTeamCompanyId;

    public User $user;

    public string $activeTab;

    public function mount(
        User $user,
        string $activeTab = 'employee-update',
        ?int $authUserId = null,
        ?int $currentTeamId = null,
        ?int $companyId = null
    ): void
    {
        $this->user = $user;
        $this->activeTab = $activeTab;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.dynamic-navigation');
    }
}
