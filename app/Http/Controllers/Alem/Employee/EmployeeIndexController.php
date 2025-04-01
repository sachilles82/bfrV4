<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeIndexController extends Controller
{
    public function index(): View
    {

        return view('laravel.alem.employee.index');
    }
}
