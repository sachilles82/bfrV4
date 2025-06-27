<?php

namespace App\Http\Controllers\Alem\Employee;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeProfileController extends Controller
{
    public function show(User $employee, string $activeTab = 'employee-update'): View
    {
        $authUser = Auth::user();

        // Nur ID und slug sind bereits geladen
        return view('laravel.alem.employee.show', [
            'employeeId' => $employee->id,
            'activeTab' => $activeTab,
            'authUserId' => $authUser->id,
            'currentTeamId' => $authUser->current_team_id,
            'companyId' => $authUser->company_id,
        ]);
    }
}
