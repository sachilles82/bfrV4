<?php

namespace App\Http\Controllers\HR\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    public function show()
    {
//        $this->authorize('show', $company);
        $user = Auth::user();

        if (!$user) {
            abort(404, 'User not found');
        }

        return view('laravel.hr.employee.profile',
            compact('user')
        );
    }
}
