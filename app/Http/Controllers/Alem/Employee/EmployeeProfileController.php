<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Enums\User\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class EmployeeProfileController extends Controller
{
    public function show(string $slug, string $activeTab = 'employee-update'): View
    {
        $authUser = Auth::user();

        // Lade Employee - wird automatisch gecached
        $employee = User::with(['currentTeam:id,name'])
            ->where('slug', $slug)
            ->where('user_type', UserType::Employee->value)
            ->firstOrFail();

        // Optional: Pre-load für bessere Performance
        User::getForComponent($employee->id, [], ['id', 'name', 'last_name']);

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
