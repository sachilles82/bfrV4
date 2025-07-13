<?php

namespace App\Http\Controllers\Alem\Employee;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeProfileController extends Controller
{
    public function show(User $user, string $activeTab = 'employee-update'): View
    {
        $authData = Auth::check() ? [
            'authUserId' => Auth::id(),
            'currentTeamId' => Auth::user()->current_team_id,
            'companyId' => Auth::user()->company_id,
        ] : null;


        return view('laravel.alem.employee.show', [
            'userId' => $user->id,
            'userSlug' => $user->url_slug, // Für Navigation
            'activeTab' => $activeTab,
            ...$authData,
        ]);
    }
}
