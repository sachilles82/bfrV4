<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Enums\User\UserType;
use App\Http\Controllers\Controller;
use App\Livewire\Alem\Employee\Holiday\HolidayTable;
use App\Livewire\Alem\Employee\Report\ReportTable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class EmployeeProfileController extends Controller
{
//    private function getComponentsForTab(string $tab): array
//    {
//        return match($tab) {
//            'employee-update' => [
////                Details::class,
////                EmploymentData::class,
//                // PersonalData::class,
//                // AddressManager::class,
//            ],
//            'report' => [
//                ReportTable::class,
//            ],
//            'holiday' => [
//                HolidayTable::class,
//            ],
//            'attendance' => [
//                // AttendanceTable::class,
//            ],
//            default => []
//        };
//    }

    public function show(string $slug, string $activeTab = 'employee-update'): View
    {
        $authUser = Auth::user();

        // Lade nur die Basis-Daten die ALLE Components brauchen
        $user = User::with([
            'currentTeam:id,name'
        ])
            ->where('slug', $slug)
            ->where('user_type', UserType::Employee->value)
            ->firstOrFail();

        // Cache nur die Basis-User-Daten
        $baseDataKey = "employee:{$user->id}:base";

        Cache::put($baseDataKey, $user, now()->addMinutes(10));

        return view('laravel.alem.employee.show', [
            'user' => $user,
            'activeTab' => $activeTab,
            'userId' => $user->id,
            'baseDataKey' => $baseDataKey,

            // Auth Daten direkt mitgeben
            'authUserId' => $authUser->id,
            'currentTeamId' => $authUser->currentTeam->id,
            'companyId' => $authUser->company_id,
        ]);
    }
}
