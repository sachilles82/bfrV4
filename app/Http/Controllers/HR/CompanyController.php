<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    use AuthorizesRequests;

    public function show()
    {
        $company = Auth::user()->company ?? abort(404);

        $this->authorize('view', $company);

        return view('laravel.hr.company.show',
            compact('company'));
    }


}
