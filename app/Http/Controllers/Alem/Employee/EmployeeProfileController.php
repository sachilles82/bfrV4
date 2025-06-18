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
        $authUser = Auth::user();

//        'employee' => $employee, erhält nur die slug und id

        return view('laravel.alem.employee.show', [
            'employee' => $employee,
            'activeTab' => $activeTab,
            'authUserId' => $authUser->id,
            'currentTeamId' => $authUser->current_team_id,
            'companyId' => $authUser->company_id,
        ]);
    }
}
