<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Livewire\Alem\Employee\Helper\WithEmployeeModelStatus;
use App\Livewire\Alem\Employee\Helper\WithEmployeeSorting;
use App\Livewire\Alem\Employee\Helper\WithEmployeeStatus;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\Paginator;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;

#[Lazy(isolate: false)]
class EmployeeTable extends Component
{
    use Searchable, WithEmployeeModelStatus, WithEmployeeSorting,
        WithEmployeeStatus,
        WithPerPagePagination;

    // Eigenschaften für vorgeladene Daten
    public int $authUserId;
    public int $currentTeamId;
    public int $companyId;

    /**
     * Tabelle zeigt nur User mit user_typ employee
     */
    protected string $userType = UserType::Employee->value;


    public function mount(int $authUserId, int $currentTeamId, int $companyId): void
    {
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;
    }

    /**
     * Hört auf das Event 'employee-created', 'employee-updated' und aktualisiert die Tabelle
     */
    #[On(['employee-created', 'employee-updated'])]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich eine öffentliche Eigenschaft ändert.
     * Setzt die Auswahl zurück, wenn Filter geändert werden.
     */
    public function updated($property): void
    {

        if (in_array($property, ['statusFilter', 'employeeStatusFilter'])) {
            $this->selectedIds = [];
            $this->reset('search', 'sortCol', 'sortAsc', 'perPage');
            $this->dispatch('update-table');
        }
    }

    /**
     * Alle Filter zurücksetzen
     */
    public function resetFilters(): void
    {
        $this->resetPage();
        $this->reset('search', 'sortCol', 'sortAsc', 'statusFilter', 'employeeStatusFilter');
        $this->selectedIds = [];
    }

    /**
     * Render the component.
     */

    public function render(): View
    {
        $authCurrentTeamId = $this->currentTeamId;
        $companyId = $this->companyId;
        $perPage = $this->perPage;
        $currentPage = Paginator::resolveCurrentPage('page');

        $query = User::select('users.id')
            ->when($this->employeeStatusFilter || $this->search, function ($q) {
                if (!collect($q->getQuery()->joins)->pluck('table')->contains('employees')) {
                    $q->join('employees', 'users.id', '=', 'employees.user_id');
                }
            })
            ->join('team_user', function ($join) use ($authCurrentTeamId) {
                $join->on('users.id', '=', 'team_user.user_id')
                    ->where('team_user.team_id', '=', $authCurrentTeamId);
            })
            ->where('users.user_type', $this->userType);

        // Filter anwenden
        $this->applySearch($query);
        $this->applyStatusFilter($query);

        if ($this->employeeStatusFilter) {
            $query->where('employees.employee_status', $this->employeeStatusFilter);
        }

        // Sortierung anwenden
        $this->applySorting($query);

        // IDs für die aktuelle Seite holen + EINE MEHR
        $userIdsIncludingNext = $query
            ->limit($perPage + 1)
            ->offset(($currentPage - 1) * $perPage)
            ->pluck('users.id');

        // Bestimme, ob es mehr Seiten gibt
        $hasMore = $userIdsIncludingNext->count() > $perPage;

        // Nimm nur die IDs für die *aktuelle* Seite
        $userIds = $userIdsIncludingNext->take($perPage);

        // === Schritt 2: Vollständige Daten für diese IDs laden ===
        $usersCollection = collect(); // Verwende einen anderen Variablennamen hier
        if ($userIds->isNotEmpty()) {
            $usersCollection = User::select([
                'users.id', 'users.department_id', 'users.name', 'users.last_name', 'users.phone_1',
                'users.email', 'users.joined_at', 'users.created_at', 'users.model_status',
                'users.profile_photo_path', 'users.slug', 'users.deleted_at',
                'employees.id as employee_id', 'employees.employee_status', 'employees.profession_id',
                'employees.stage_id', 'professions.name as profession_name',
                'stages.name as stage_name', 'departments.name as department_name'
            ])
                ->join('employees', 'users.id', '=', 'employees.user_id')
                ->leftJoin('professions', 'employees.profession_id', '=', 'professions.id')
                ->leftJoin('stages', 'employees.stage_id', '=', 'stages.id')
                ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
                ->with(['roles' => function ($q_roles) use ($companyId) {
                    $q_roles->where('visible', RoleVisibility::Visible->value)
                        ->where('access', RoleHasAccessTo::EmployeePanel->value)
                        ->where(function($query) use ($companyId) {
                            $query->where('created_by', 1)
                                ->orWhere('company_id', $companyId);
                        })
                        ->select('roles.id', 'roles.name');
                },
                ])
                ->whereIn('users.id', $userIds)
                // ->orderBy($this->sortCol ?: 'users.created_at', $this->sortAsc ? 'asc' : 'desc') // Sortierung ist hier nicht mehr nötig, da wir nach IDs sortieren
                ->get();

            // Sortiere die Ergebnisse manuell basierend auf der Reihenfolge der IDs aus Schritt 1
            $userIdsOrder = $userIds->flip();
            $usersCollection = $usersCollection->sortBy(function ($user) use ($userIdsOrder) {
                return $userIdsOrder[$user->id] ?? PHP_INT_MAX;
            });
        }

        $paginator = new Paginator(
            $usersCollection,
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'page']
        );

        $this->idsOnPage = $usersCollection->pluck('id')->map(fn($id) => (string)$id)->toArray();

        // Übergebe den Paginator UND die $hasMore Information an die View
        return view('livewire.alem.employee.table', [
            'users' => $paginator,
            'statuses' => ModelStatus::cases(),
            'employeeStatuses' => EmployeeStatus::cases(),
            'hasMorePagesBoolean' => $hasMore,
        ]);
    }
//    public function render(): View
//    {
//        $authCurrentTeamId = $this->currentTeamId;
//        $companyId = $this->companyId;
//
//        $query = User::select([
//            'users.id',
//            'users.department_id',
//            'users.name',
//            'users.last_name',
//            'users.phone_1',
//            'users.email',
//            'users.joined_at',
//            'users.created_at',
//            'users.model_status',
//            'users.profile_photo_path',
//            'users.slug',
//            'users.deleted_at',
//            'employees.id as employee_id',
//            'employees.employee_status',
//            'employees.profession_id',
//            'employees.stage_id',
//            'professions.name as profession_name',
//            'stages.name as stage_name',
//            'departments.name as department_name'
//        ])
//            ->join('employees', 'users.id', '=', 'employees.user_id')
//            ->join('team_user', function ($join) use ($authCurrentTeamId) {
//                $join->on('users.id', '=', 'team_user.user_id')
//                    ->where('team_user.team_id', '=', $authCurrentTeamId);
//            })
//            ->leftJoin('professions', 'employees.profession_id', '=', 'professions.id')
//            ->leftJoin('stages', 'employees.stage_id', '=', 'stages.id')
//            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
//            ->where('users.user_type', $this->userType);
//
//        $query->with([
//            'roles' => function ($q_roles) use ($companyId) {
//                $q_roles->where('visible', RoleVisibility::Visible->value)
//                    ->where('access', RoleHasAccessTo::EmployeePanel->value)
//                    ->where(function($query) use ($companyId) {
//                        $query->where('created_by', 1)
//                            ->orWhere('company_id', $companyId);
//                    })
//                    ->select('roles.id', 'roles.name');
//            },
//        ]);
//
//
//        $this->applySearch($query);
//        $this->applySorting($query);
//        $this->applyStatusFilter($query);
//        //EmployeeStatusFilter
//        if ($this->employeeStatusFilter) {
//            $query->where('employees.employee_status', $this->employeeStatusFilter);
//        }
//
//        // Entfernt Duplikate, die durch JOINs entstehen könnten
////        $query->distinct();
//
//        $users = $query->simplePaginate($this->perPage);
//        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();
//
//        return view('livewire.alem.employee.table', [
//            'users' => $users,
//            'statuses' => ModelStatus::cases(),
//            'employeeStatuses' => EmployeeStatus::cases(),
//        ]);
//    }

    public function placeholder():View
    {
        return view('livewire.placeholders.employee.index');
    }
}
