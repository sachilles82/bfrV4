<?php

namespace App\Livewire\Alem\Employee\Profile;

use App\Models\User;
use App\Models\Alem\Employee;
use App\Traits\User\AuthUserTeamCompanyId;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class ProfileManager extends Component
{
    use AuthUserTeamCompanyId;

    // Shared Data
    public ?User $employee = null;
    public ?Employee $employeeModel = null;
    public int $employeeId;
    public string $activeTab;

    // Cache Keys
    protected string $baseCacheKey;
    protected string $fullCacheKey;

    public function mount(
        int $employeeId,
        string $activeTab,
        int $authUserId,
        int $currentTeamId,
        int $companyId
    ): void {
        $this->employeeId = $employeeId;
        $this->activeTab = $activeTab;
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;

        // Cache Keys definieren
        $this->baseCacheKey = "employee:{$employeeId}:base";
        $this->fullCacheKey = "employee:{$employeeId}:full";

        // Lade alle Daten EINMAL
        $this->loadEmployeeData();
    }

    private function loadEmployeeData(): void
    {
        // WICHTIG: Request-Cache für duplicate prevention
        $requestCacheKey = "request_employee_{$this->employeeId}";

        // Prüfe zuerst Request-Cache
        if (isset($GLOBALS[$requestCacheKey])) {
            $this->employee = $GLOBALS[$requestCacheKey];
            $this->employeeModel = $this->employee->employee;
            return;
        }

        // Dann persistent Cache
        $this->employee = Cache::remember($this->fullCacheKey, now()->addMinutes(10), function () {
            return User::with([
                // Employee mit allen benötigten Feldern
                'employee' => function ($query) {
                    $query->select([
                        'id', 'user_id',
                        // Details Component
                        // Employment Data
                        'ahv_number', 'birthdate', 'nationality',
                        'hometown', 'religion', 'civil_status',
                        'residence_permit',
                        // Personal Data
                        'employee_status', 'personal_number',
                        'employment_type', 'supervisor_id',
                        'probation_enum', 'probation_at',
                        'notice_enum', 'notice_at', 'leave_at',
                        'profession_id', 'stage_id'
                    ]);
                },
                // Relations für alle Components
                'employee.profession:id,name',
                'employee.stage:id,name',
                'teams' => function($query) {
                    $query->select('teams.id', 'teams.name');
                },
                'roles' => function($query) {
                    $query->select('roles.id', 'roles.name', 'roles.is_manager');
                },
                'department:id,name',
                'currentTeam:id,name'
            ])
                ->select([
                    'id', 'slug', 'name', 'last_name', 'email',
                    'phone_1', 'gender', 'model_status',
                    'department_id', 'joined_at', 'company_id'
                ])
                ->find($this->employeeId);
        });

        if ($this->employee) {
            $this->employeeModel = $this->employee->employee;
            // Speichere in Request-Cache
            $GLOBALS[$requestCacheKey] = $this->employee;
        }
    }

    /**
     * Refresh Daten wenn Updates in Child Components passieren
     */
    #[On(['employee-basic-data-updated', 'employment-data-updated', 'personal-data-updated'])]
    public function refreshEmployeeData(): void
    {
        // Cache invalidieren
        Cache::forget($this->fullCacheKey);
        Cache::forget($this->baseCacheKey);

        // Neu laden
        $this->loadEmployeeData();

        // Child Components benachrichtigen
        $this->dispatch('employee-data-refreshed', employee: $this->employee);
    }

    /**
     * Helper Methode für Child Components um spezifische Daten zu bekommen
     */
    public function getEmployeeData(array $fields = []): array
    {
        if (empty($fields)) {
            return [
                'employee' => $this->employee,
                'employeeModel' => $this->employeeModel
            ];
        }

        $data = [];
        foreach ($fields as $field) {
            if (str_contains($field, '.')) {
                [$relation, $attribute] = explode('.', $field, 2);
                $data[$field] = data_get($this->employee, $field);
            } else {
                $data[$field] = $this->employee->$field ?? null;
            }
        }

        return $data;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.profile-manager');
    }
}
