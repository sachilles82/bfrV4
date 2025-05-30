<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class EmployeeIndexController extends Controller
{

    public function index(): View
    {
        $authUser = Auth::user();
        $currentTeamId = $authUser->currentTeam->id;
        $companyId = $authUser->company_id;

        return view('laravel.alem.employee.index',

            [
            'authUserId' => $authUser->id,
            'currentTeamId' => $currentTeamId,
            'companyId' => $companyId,
        ]

        );
    }

}
