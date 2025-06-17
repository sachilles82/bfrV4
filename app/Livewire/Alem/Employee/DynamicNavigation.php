<?php

namespace App\Livewire\Alem\Employee;

use App\Models\User;
use App\Traits\User\AuthUserTeamCompanyId;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class DynamicNavigation extends Component
{
    use AuthUserTeamCompanyId;

    public User $employee;
    public int $employeeId;
    public string $activeTab;

    public function mount(
        User $employee,
        string $activeTab,
        int $employeeId,
        int $authUserId,
        int $currentTeamId,
        int $companyId
    ): void {
        $this->employee = $employee;
        $this->employeeId = $employeeId;
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
