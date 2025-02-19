<?php

namespace App\Http\Controllers\Alem\Company;

use App\Http\Controllers\Controller;
use App\Models\Alem\Department;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DepartmentController extends Controller
{
    use AuthorizesRequests;

    public function show(Department $department)
    {
//        $this->authorize('show', $department);
        return view('laravel.alem.department.show',
            compact('department')
        );
    }
}
