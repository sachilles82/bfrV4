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
        // Lade die Company des angemeldeten Nutzers mit nur den benötigten Feldern
        // und eager lade die Adresse sowie die zugehörigen Länder-, State- und City-Daten.
        $company = Auth::user()->company()
            ->select('id', 'owner_id', 'company_name', 'industry_id', 'company_size', 'company_type', 'email', 'phone_1', 'phone_2', 'register_number', 'company_url')
            ->with([
                'address' => function($query) {
                    $query->select('id', 'addressable_id', 'addressable_type', 'country_id', 'state_id', 'city_id', 'street_number');
                },
                'address.country:id,name',
                'address.state:id,name',
                'address.city:id,name',

            ])
            ->first();

        if (!$company) {
            abort(404);
        }

//        $this->authorize('view', $company);

        return view('laravel.hr.company.show', compact('company'));
    }
}
