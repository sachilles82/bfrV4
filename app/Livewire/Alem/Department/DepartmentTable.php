<?php

namespace App\Livewire\Alem\Department;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Department\Helper\Searchable;
use App\Livewire\Alem\Department\Helper\WithDepartmentSorting;
use App\Models\Alem\Department;
use App\Traits\Model\ModelStatusAction;
use App\Traits\Table\WithPerPagePagination;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DepartmentTable extends Component
{
    use Searchable, WithPerPagePagination, WithDepartmentSorting,
        ModelStatusAction;

    public $selectedIds = [];
    public $idsOnPage = [];
    public $statusFilter = 'active';

    public $departmentId;
    public $name = '';


    /**
     * Die Modellklasse für ModelStatusAction
     */
    protected function getModelClass(): string
    {
        return Department::class;
    }

    /**
     * Der Anzeigename für das Modell
     */
    protected function getModelDisplayName(): string
    {
        return 'Department';
    }

    /**
     * Der pluralisierte Anzeigename für das Modell
     */
    protected function getModelDisplayNamePlural(): string
    {
        return 'Departments';
    }

    /**
     * Der Benutzertyp für die Filterung
     */
    protected string $DepartmentType = 'department';

    /**
     * Name des Events, das nach Status-Änderungen ausgelöst wird
     */
    protected function getStatusUpdateEventName(): string
    {
        return 'department-updated';
    }

    /**
     * Hört auf das Event 'department-created', 'department-updated' und aktualisiert die Tabelle
     */
    #[On(['department-created', 'department-updated'])]
    public function refreshTable(): void
    {
        $this->resetPage();
    }

    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich ein Filter ändert, die Auswahl zurücksetzen
     */
    public function updated($property): void
    {
        if (in_array($property, ['statusFilter'])) {
            $this->selectedIds = [];
            $this->reset('search', 'sortCol', 'sortAsc');
            $this->dispatch('update-table');
        }
    }

    /**
     * Alle Filter zurücksetzen
     */
    public function resetFilters(): void
    {
        $this->resetPage();
        $this->reset('search');
        $this->reset('sortCol', 'sortAsc', 'statusFilter');
        $this->selectedIds = [];
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    /**
     * Öffnet den Bearbeitungsmodus für einen Mitarbeiter
     */
    public function edit($id): void
    {
        $this->dispatch('edit-department', $id);
    }

//    #[On('created')]
//    public function handleCreated(): void
//    {
//        $this->resetPage();
//        $this->reset('search');
//        $this->dispatch('update-table');
//    }

    public function render(): View
    {
        $query = Department::with(['creator', 'team', 'company', 'users']);

        $this->applySearch($query);
        $this->applySorting($query);
        $this->applyStatusFilter($query);


        $departments = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->idsOnPage =$departments->pluck('id')->map(fn($id) => (string)$id)->toArray();


        return view('livewire.alem.department.table', [
            'departments' => $departments,
            'statuses' => ModelStatus::cases(),
        ]);
    }

    /**
     * Bereitet die Benutzerdaten für die Anzeige der Avatare vor
     *
     * @param \App\Models\Alem\Department $department
     * @return array
     */
    public function prepareUserAvatars($department): array
    {
        $userCount = $department->users->count();

        $result = [
            'has_users' => $userCount > 0,
            'total_count' => $userCount,
            'visible_users' => [],
            'remaining_count' => 0,
            'remaining_user_groups' => [],
        ];

        if ($result['has_users']) {
            // Sichtbare Benutzer (max 3)
            $visibleUsers = $department->users->take(3);

            foreach ($visibleUsers as $index => $user) {
                $result['visible_users'][] = [
                    'name' => $user->name,
                    'last_name' => $user->last_name ?? '',
                    'full_name' => trim($user->name . ' ' . ($user->last_name ?? '')),
                    'avatar_url' => "https://ui-avatars.com/api/?name=" . urlencode($user->name) . "&color=7F9CF5&background=EBF4FF",
                    'z_index' => 30 - ($index + 1) * 10
                ];
            }

            // Restliche Benutzer für den Tooltip
            if ($userCount > 3) {
                $result['remaining_count'] = $userCount - 3;

                // Bereite jeden verbleibenden Benutzer als separaten Eintrag für den Tooltip vor
                $remainingUsers = $department->users->skip(3);

                foreach ($remainingUsers as $user) {
                    $result['remaining_user_groups'][] = trim($user->name . ' ' . ($user->last_name ?? ''));
                }
            }
        }

        return $result;
    }
}
