<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    public function show()
    {
        $authUser = Auth::user();

        if (! $authUser) {
            abort(404, 'User not found');
        }

        return view('laravel.alem.employee.profile',
            compact('authUser')
        );
    }
}
