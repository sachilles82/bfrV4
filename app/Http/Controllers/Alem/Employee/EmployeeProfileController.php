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
        $userEmployeeDetails = User::query()
            ->select([
                'id',
                'gender',
                'name',
                'last_name',
                'email',
                'phone_1',
                'model_status',
                'department_id',
                'slug', // Für URL routing
            ])
            ->with([
                // Lade nur die ID und Name der Relations
                'teams:id,name',
                'roles:id,name',
                'department:id,name'
            ])
            ->where('id', $employee->id)
            ->first();

        $authUser = Auth::user();

        return view('laravel.alem.employee.show', [
            'employee' => $userEmployeeDetails,
            'activeTab' => $activeTab,
            'authUserId' => $authUser->id,
            'currentTeamId' => $authUser->current_team_id,
            'companyId' => $authUser->company_id,
        ]);
    }
}
