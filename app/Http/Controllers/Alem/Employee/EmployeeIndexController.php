<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Http\Controllers\Controller;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\Team;

// Team Model importieren
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EmployeeIndexController extends Controller
{
    /**
     * Generiert einen Cache-Schlüssel für firmenweite Listen-Daten.
     *
     * @param string $resourceName Name der Ressource (z.B. 'departments', 'teams')
     * @param int $companyId
     * @return string|null Cache-Schlüssel oder null, wenn Company fehlt
     */
    private function getCompanyCacheKey(string $resourceName, ?int $companyId): ?string // Nimmt jetzt companyId entgegen
    {
        if (!$companyId) {
            Log::warning("Versuch, Cache-Schlüssel ohne Company-ID zu generieren.", ['resource' => $resourceName]);
            return null;
        }
        // Einfacherer Schlüssel: Nur Firma und Ressource
        return "list:company:{$companyId}:resource:{$resourceName}";
    }

    // getCompanyListTag wird nicht mehr benötigt

    public function index(): View
    {
        $authCompanyId = Auth::user()?->company_id;

        if (!$authCompanyId) {
            abort(403, 'Keine Firma zugeordnet.');
        }

        // --- Departments laden ---
        $departmentsKey = $this->getCompanyCacheKey('departments', $authCompanyId);
        $departments = null;
        if ($departmentsKey) {
            // Cache::tags(...) entfernt!
            $departments = Cache::rememberForever($departmentsKey, function () use ($authCompanyId, $departmentsKey) {
                return Department::where('company_id', $authCompanyId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all();
            });
        } else {
            $departments = [];
        }


        // --- Teams laden ---
        $teamsKey = $this->getCompanyCacheKey('teams', $authCompanyId);
        $teams = null;
        if ($teamsKey) {
            $teams = Cache::rememberForever($teamsKey, function () use ($authCompanyId, $teamsKey) { // Nur noch $authCompanyId und $teamsKey in use()
                Log::info("Cache für Teams der Firma {$authCompanyId} (Key: {$teamsKey}) neu aufgebaut.");
                return Team::where('company_id', $authCompanyId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all();
            });
        } else {
            $teams = [];
        }


        // --- Professions laden ---
        $professionsKey = $this->getCompanyCacheKey('professions', $authCompanyId);
        $professions = null;
        if ($professionsKey) {
            $professions = Cache::rememberForever($professionsKey, function () use ($authCompanyId, $professionsKey) {
                return Profession::where('company_id', $authCompanyId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all();
            });
        } else {
            $professions = [];
            Log::error("Konnte Profession Cache Key nicht generieren.");
        }


        // --- Stages laden ---
        $stagesKey = $this->getCompanyCacheKey('stages', $authCompanyId);
        $stages = null;
        if ($stagesKey) {
            $stages = Cache::rememberForever($stagesKey, function () use ($authCompanyId, $stagesKey) {
                Log::info("Cache für Stages (Key: {$stagesKey}) neu aufgebaut.");
                return Stage::where('company_id', $authCompanyId)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all();
            });
        } else {
            $stages = [];
        }

        return view('laravel.alem.employee.index', [
            'departments' => $departments ?? [],
            'teams' => $teams ?? [],
            'professions' => $professions ?? [],
            'stages' => $stages ?? [],
        ]);
    }
}
