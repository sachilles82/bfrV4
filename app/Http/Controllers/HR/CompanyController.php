<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function show(): View
    {
        $companyId = Auth::user()->company()->value('id');
        $cacheKey = "company-details-{$companyId}";

        // Cache den Datensatz (60 Minuten TTL), da sich die Daten selten ändern.
        $company = Cache::rememberForever($cacheKey, function () {
            return Auth::user()->company()
                ->select([
                    'id', 'owner_id', 'company_name', 'industry_id',
                    'company_size', 'company_type', 'email', 'phone_1',
                    'phone_2', 'register_number', 'company_url'
                ])
                ->with([
                    'address' => function($query) {
                        $query->select('id', 'addressable_id', 'addressable_type', 'country_id', 'state_id', 'city_id', 'street_number');
                    },
                    'address.country:id,name',
                    'address.state:id,name',
                    'address.city:id,name',
                ])
                ->firstOrFail();
        });

        return view('laravel.hr.company.show',
            compact('company')
        );
    }
}
