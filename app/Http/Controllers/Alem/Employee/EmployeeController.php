<?php

namespace App\Http\Controllers\Alem\Employee;

use App\Enums\User\UserType;
use App\Http\Controllers\Controller;
use App\Livewire\Alem\Employee\Holiday\HolidayTable;
use App\Livewire\Alem\Employee\Profile\Account\Details;
use App\Livewire\Alem\Employee\Profile\EmploymentData;
use App\Livewire\Alem\Employee\Report\ReportTable;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    private function getComponentsForTab(string $tab): array
    {
        return match($tab) {
            'employee-update' => [
                Details::class,
//                EmploymentData::class,
                // PersonalData::class,
                // AddressManager::class,
            ],
            'report' => [
                ReportTable::class,
            ],
            'holiday' => [
                HolidayTable::class,
            ],
            'attendance' => [
                // AttendanceTable::class,
            ],
            default => []
        };
    }

    public function show(string $slug, string $activeTab = 'employee-update'): View
    {
        $authUser = Auth::user();

        // 1. Sammle alle benötigten Relations für den Tab
        $relations = $this->collectRequiredRelations($activeTab);

        // 2. Ein optimierter Query mit allen Relations
        $user = User::with($relations)
            ->where('slug', $slug)
            ->where('user_type', UserType::Employee->value)
            ->firstOrFail();

        // 3. Cache die Daten für die Components
        $sharedDataKey = "employee:{$user->id}:tab:{$activeTab}";
        Cache::put($sharedDataKey, $user, now()->addMinutes(10));

        return view('laravel.alem.employee.show', [
            'user' => $user,
            'activeTab' => $activeTab,
            'sharedDataKey' => $sharedDataKey,
            // Auth Daten direkt mitgeben
            'authUserId' => $authUser->id,
            'currentTeamId' => $authUser->currentTeam->id,
            'companyId' => $authUser->company_id,
        ]);
    }

    private function collectRequiredRelations(string $tab): array
    {
        $relations = ['company', 'currentTeam']; // Basis

        foreach ($this->getComponentsForTab($tab) as $componentClass) {
            if (method_exists($componentClass, 'requiredRelations')) {
                $relations = array_merge($relations, $componentClass::requiredRelations());
            }
        }

        return array_unique($relations);
    }
}
