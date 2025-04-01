<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Zeigt das Profil eines Mitarbeiters an
     *
     * @param string $slug Der Slug des Mitarbeiters (z.B. 'vorname-nachname-index')
     * @param string $activeTab Der aktive Tab in der Ansicht (Standard ist 'employee-update')
     * @return View
     */
    public function show($slug, $activeTab = 'employee-update'): View
    {
        // Finde den User basierend auf Slug
        $user = User::where('slug', $slug)->first();

        if (!$user || $user->user_type !== 'employee' || !$user->employee) {
            abort(404, 'Mitarbeiter nicht gefunden.');
        }

        return view('laravel.alem.employee.show',
            compact('user', 'activeTab')
        );
    }
}
