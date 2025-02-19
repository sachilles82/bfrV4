<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function show(User $user, $activeTab = 'employee-update')
    {
        if ($user->user_type !== 'employee' || !$user->employee) {
            abort(404, 'Employee not found.');
        }

        return view('laravel.alem.employee.show',
            compact('user', 'activeTab')
        );
    }
}
