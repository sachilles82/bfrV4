<?php

namespace App\Livewire\Alem\Department;

use App\Enums\Model\ModelStatus;
use App\Livewire\Alem\Department\Helper\Searchable;
use App\Livewire\Alem\Department\Helper\ValidateDepartment;
use App\Models\Alem\Department;
use App\Traits\Model\ModelStatusAction;
use App\Traits\Table\WithPerPagePagination;
use App\Traits\Table\WithSorting;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DepartmentTable extends Component
{
    use Searchable, WithPerPagePagination, WithSorting, ValidateDepartment, ModelStatusAction;

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
        return 'departmentUpdated';
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
     * Massenänderung des Status für ausgewählte Departments
     */
    public function bulkStatusChange($status): void
    {
        if (count($this->selectedIds) > 0) {
            Department::whereIn('id', $this->selectedIds)->update(['model_status' => $status]);
            $this->selectedIds = [];
            $this->dispatch('update-table');
            $this->dispatch('departmentUpdated');
            
            Flux::toast(
                text: __('Departments status updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }
    }

    public function loadDepartment(): void
    {
        $department = Department::find($this->departmentId);
        $this->name = $department->name;
    }

    /**
     * Öffnet das Modal zum Bearbeiten einer Abteilung
     *
     * @param int $id ID der zu bearbeitenden Abteilung
     */
    public function edit(int $id): void
    {
        // Department laden
        $department = Department::findOrFail($id);
        
        // Daten an die CreateDepartment-Komponente weitergeben
        $this->dispatch('load-department-data', 
            departmentId: $department->id,
            name: $department->name,
            description: $department->description,
            status: $department->model_status
        );
    }

    public function showEditModal($id): void
    {
        $this->dispatch('open-department-edit', departmentId: $id);
    }

    public function update(): void
    {
        // Authorization hinzufügen
        $this->validate();

        $department = Department::find($this->departmentId);

        $department->update($this->only([
            'name'
        ]));

        $this->modal('department-edit')->close();
        $this->dispatch('department-updated');
        
        Flux::toast(
            text: __('Department updated successfully.'),
            heading: __('Success.'),
            variant: 'success'
        );
    }

    /** Delete Function **/
    public function delete($id): void
    {
        $department = Department::find($id);

        // Authorization hinzufügen
        if ($department) {
            $department->delete();
            
            Flux::toast(
                text: __('Department deleted successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );
            
            $this->dispatch('department-updated');
        }
    }
    /** Delete Function **/

    #[On('created')]
    public function handleCreated(): void
    {
        $this->resetPage();
        $this->reset('search');
        $this->dispatch('update-table');
    }

    public function render(): View
    {
        $query = Department::with(['creator', 'team', 'company', 'users']);

        // Wende den Status-Filter an (aus dem ModelStatusAction Trait)
        $this->applyStatusFilter($query);

        // Verwende den Suchbegriff, wenn einer vorhanden ist
        if ($this->search) {
            $query->where(function ($query) {
                $query->whereTranslationLike('name', '%' . $this->search . '%')
                    ->orWhereTranslationLike('description', '%' . $this->search . '%');
            });
        }

        // Wende die Sortierung an
        if ($this->sortCol) {
            $query->orderBy($this->sortCol, $this->sortAsc ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Hole deine aktuelle Seite mit Pagination von deinem Trait
        $departments = $this->applySimplePagination($query);

        // Setze die IDs auf der Seite
        $this->idsOnPage = $departments->pluck('id')->toArray();

        // Liste aller möglichen Status (für Filter)
        $statuses = ModelStatus::cases();

        return view('livewire.alem.department.table', [
            'departments' => $departments,
            'statuses' => $statuses,
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
