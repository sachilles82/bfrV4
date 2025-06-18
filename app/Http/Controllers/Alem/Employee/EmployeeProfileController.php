<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Enums\User\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeProfileController extends Controller
{
    public function show(User $employee, string $activeTab = 'employee-update'): View
    {
        // Lade Employee mit allen benötigten Relations für erste Component
        $employee->load([
            'teams:id,name',
            'roles:id,name',
            'department:id,name'
        ]);

        $authUser = Auth::user();

        return view('laravel.alem.employee.show', [
            'employee' => $employee,
            'activeTab' => $activeTab,
            'authUserId' => $authUser->id,
            'currentTeamId' => $authUser->current_team_id,
            'companyId' => $authUser->company_id,
        ]);
    }
}
