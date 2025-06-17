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

        // Lade nur die Basis-Daten die ALLE Components brauchen
        $employee = User::with(['currentTeam:id,name'])
            ->where('slug', $slug)
            ->where('user_type', UserType::Employee->value)
            ->firstOrFail();

        // Cache nur die Basis-User-Daten
        $baseDataKey = "employee:{$employee->id}:base";

        Cache::put($baseDataKey, $employee, now()->addMinutes(10));

        return view('laravel.alem.employee.show', [
            'employee' => $employee,  // Statt 'user'
            'employeeId' => $employee->id,  // Statt 'userId'
            'activeTab' => $activeTab,
            'baseDataKey' => $baseDataKey,

            // Auth Daten direkt mitgeben
            'authUserId' => $authUser->id,
            'currentTeamId' => $authUser->currentTeam->id,
            'companyId' => $authUser->company_id,
        ]);
    }
}
