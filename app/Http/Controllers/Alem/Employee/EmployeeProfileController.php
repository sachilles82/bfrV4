<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Enums\User\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeProfileController extends Controller
{
    public function show(string $slug, string $activeTab = 'employee-update'): View
    {
        $authUser = Auth::user();

        // Lade nur minimale Daten für Navigation
        // Der ProfileManager Component lädt alle Details
        $employee = User::select('id', 'slug', 'name', 'last_name', 'company_id')
            ->where('slug', $slug)
            ->where('user_type', UserType::Employee->value)
            ->firstOrFail();

        return view('laravel.alem.employee.show', [
            'employee' => $employee,
            'employeeId' => $employee->id,
            'activeTab' => $activeTab,
            'authUserId' => $authUser->id,
            'currentTeamId' => $authUser->currentTeam->id,
            'companyId' => $authUser->company_id,
        ]);
    }
}
