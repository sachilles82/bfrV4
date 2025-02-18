<?php

namespace App\Http\Controllers\Account\Employee;

use App\Http\Controllers\Controller;
use App\Models\Account\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{

    public function show(Employee $employee)
    {
//        $this->authorize('show', $employee);
        return view('laravel.account.employees.show',
            compact('employee'));
    }

    public function profile(Employee $employee)
    {
        // Optional: Autorisierung prüfen
        // $this->authorize('view', $employee);
        return view('laravel.account.employees.show', compact('employee'));
    }
}
