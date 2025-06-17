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
    public string $sharedDataKey;

    public function mount(
        User $user,
        string $activeTab,
        string $sharedDataKey,
        int $authUserId,
        int $currentTeamId,
        int $companyId
    ): void {
        $this->user = $user;
        $this->activeTab = $activeTab;
        $this->sharedDataKey = $sharedDataKey;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.dynamic-navigation');
    }
}
