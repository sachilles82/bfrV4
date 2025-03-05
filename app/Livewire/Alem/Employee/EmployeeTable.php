<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\User\EmployeeAccountStatus;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use App\Traits\Table\WithSorting;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithPerPagePagination, WithSorting;

    public $selectedIds = [];
    public $idsOnPage = [];
    public $name = '';

    // Filter: active, not_activated, archived, trash
    public $statusFilter = 'active';

    /**
     * Setzt den Status-Filter (z. B. beim Klick auf einen Wert in der Tabelle)
     */
    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
    }

    /**
     * Führt eine Bulk-Aktion auf alle ausgewählten Mitarbeiter aus.
     */
    public function bulkUpdateStatus(string $action): void
    {
        $users = User::whereIn('id', $this->selectedIds)->get();

        foreach ($users as $user) {
            if ($action === 'active') {
                // Falls der User gelöscht wurde, wiederherstellen
                if ($user->trashed()) {
                    $user->restore();
                }
                // Status auf Active setzen
                $user->update([
                    'account_status' => EmployeeAccountStatus::ACTIVE->value
                ]);
            } elseif ($action === 'not_activated') {
                // Status auf Not Activated setzen
                $user->update([
                    'account_status' => EmployeeAccountStatus::NOT_ACTIVATED->value
                ]);
            } elseif ($action === 'archived') {
                // Archivieren und Account-Status auf "archived" setzen
                $user->update([
                    'account_status' => EmployeeAccountStatus::ARCHIVED->value
                ]);
            } elseif ($action === 'trash') {
                // Falls noch nicht gelöscht, soft-delete durchführen
                if (!$user->trashed()) {
                    $user->delete(); // This will also set account_status to 'trashed'
                }
            }
        }

        // Nach Bulk-Aktion die ausgewählten IDs leeren
        $this->selectedIds = [];
        $this->idsOnPage = [];
        $this->resetFilters();

        $this->dispatch('employeeUpdated');
    }

    /**
     * Permanently deletes multiple employees from trash.
     */
    public function bulkForceDelete(): void
    {
        $users = User::withTrashed()->whereIn('id', $this->selectedIds)->get();
        $count = 0;

        foreach ($users as $user) {
            if ($user->trashed()) {
                $user->forceDelete();
                $count++;
            }
        }

        $this->selectedIds = [];
        $this->idsOnPage = [];
        $this->dispatch('employeeUpdated');

        if ($count > 0) {
            Flux::toast(
                text: __(':count employees permanently deleted.', ['count' => $count]),
                heading: __('Success.'),
                variant: 'danger'
            );
        }
    }

    /**
     * Setzt Filter zurück.
     */
    public function resetFilters(): void
    {
        $this->reset('search', 'sortCol', 'sortAsc', 'statusFilter');
        $this->dispatch('resetFilters');
    }


    /**
     * Soft-deletes einen Mitarbeiter.
     */
    public function delete($userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete(); // This will also set account_status to 'trashed'
            $this->dispatch('employeeUpdated');

            Flux::toast(
                text: __('Employee moved to trash.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }
    }

    /**
     * Setzt den Benutzer auf "Nicht aktiviert"
     */
    public function notActivate($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== EmployeeAccountStatus::NOT_ACTIVATED->value) {
            $user->update([
                'account_status' => EmployeeAccountStatus::NOT_ACTIVATED->value
            ]);

            $this->dispatch('employeeUpdated');

            Flux::toast(
                text: __('Employee set to not activated.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }
    }

    /**
     * Archiviert einen Mitarbeiter (setzt account_status auf 'archived').
     */
    public function archive($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== EmployeeAccountStatus::ARCHIVED->value) {
            $user->update([
                'account_status' => EmployeeAccountStatus::ARCHIVED->value
            ]);

            $this->dispatch('employeeUpdated');

            Flux::toast(
                text: __('Employee archived.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }
    }

    /**
     * Stellt einen archivierten oder gelöschten Mitarbeiter wieder her.
     */
    public function restore($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if ($user) {
            if ($user->trashed()) {
                $user->restore(); // This will also restore account_status

                Flux::toast(
                    text: __('Employee restored from trash.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            } elseif ($user->account_status === EmployeeAccountStatus::ARCHIVED->value) {
                $user->update([
                    'account_status' => EmployeeAccountStatus::ACTIVE->value
                ]);

                Flux::toast(
                    text: __('Employee restored from archive.'),
                    heading: __('Success.'),
                    variant: 'success'
                );
            }
            $this->dispatch('employeeUpdated');
        }
    }

    public function restoreToArchive($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if ($user && $user->trashed()) {
            $user->restore();  // First restore from trash
            $user->update([    // Then set to archived
                'account_status' => EmployeeAccountStatus::ARCHIVED->value
            ]);

            Flux::toast(
                text: __('Employee restored to archive.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('employeeUpdated');
        }
    }

    /**
     * Aktiviert einen nicht aktivierten Mitarbeiter.
     */
    public function activate($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== EmployeeAccountStatus::ACTIVE->value) {
            $user->update([
                'account_status' => EmployeeAccountStatus::ACTIVE->value
            ]);
            $this->dispatch('employeeUpdated');

            Flux::toast(
                text: __('Employee activated.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }
    }

    /**
     * Löscht einen Mitarbeiter dauerhaft (nur aus dem Papierkorb).
     */
    public function forceDelete($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if ($user) {
            $user->forceDelete();

            Flux::toast(
                text: __('Employee permanently deleted.'),
                heading: __('Success.'),
                variant: 'danger'
            );

            $this->dispatch('employeeUpdated');
        }
    }

    /**
     * Wendet den Status-Filter an.
     */
    protected function applyStatusFilter($query)
    {
        switch ($this->statusFilter) {
            case 'not_activated':
                $query->notActivated();
                break;
            case 'archived':
                $query->archived();
                break;
            case 'trash':
                $query->onlyTrashed();
                break;
            case 'active':
            default:
                $query->active();
        }
        return $query;
    }

    protected function applySorting($query)
    {
        if ($this->sortCol) {
            $column = match ($this->sortCol) {
                'name' => 'name',
                'email' => 'email',
                default => 'created_at',
            };
            $query->orderBy($column, $this->sortAsc ? 'asc' : 'desc');
        }
        return $query;
    }

    /**
     * Rendert die Employee-Tabelle.
     */
    public function render(): View
    {
        $authUser = auth()->user();

        $query = User::query()
            ->with(['employee', 'teams:id,name', 'roles:id,name'])
            ->where('company_id', $authUser->company_id)
            ->where('user_type', 'employee');

        $this->applySearch($query);
        $this->applySorting($query);
        $this->applyStatusFilter($query);

        $users = $query->orderBy('created_at', 'desc')->paginate($this->perPage);
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        return view('livewire.alem.employee.table', [
            'users' => $users,
        ]);
    }
}
