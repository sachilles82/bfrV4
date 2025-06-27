<?php

namespace App\Http\Controllers\Alem\Employee;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeProfileController extends Controller
{
    public function show(User $user, string $activeTab = 'employee-update'): View
    {
        $authUser = Auth::user();

        // Nur ID und slug sind bereits geladen
        return view('laravel.alem.employee.show', [
            'userId' => $user->id,
            'activeTab' => $activeTab,
            'authUserId' => $authUser->id,
            'currentTeamId' => $authUser->current_team_id,
            'companyId' => $authUser->company_id,
        ]);
    }
}
//public function mount(int $employeeId, int $authUserId, int $currentTeamId, int $companyId): void
//{
//    $this->employeeId = $employeeId;
//    $this->authUserId = $authUserId;
//    $this->currentTeamId = $currentTeamId;
//    $this->companyId = $companyId;
//
//    // Lade Employee mit allen benötigten Relations
//    $this->employee = User::with([
//        'teams:id,name',
//        'roles:id,name,is_manager',
//    ])
//        ->select([
//            'id', 'name', 'email', 'gender', 'model_status',
//            'department_id', 'phone_1', 'company_id', 'manager','supervisor_id'
//        ])
//        ->findOrFail($this->employeeId);
//
//    $this->loadEmployeeData();
//
////        // Lade Dropdown-Daten
//    $this->loadRelationsData([
//        'teams', 'departments', 'roles', 'supervisors'
//    ]);
//}
