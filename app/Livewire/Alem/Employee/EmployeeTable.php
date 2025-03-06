<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\User\AccountStatus;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use App\Traits\Table\WithSorting;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithPerPagePagination, WithSorting;

    public $selectedIds = [];
    public $idsOnPage = [];
    public $name = '';
    public $statusFilter = 'active';

    /* Status-Filter */
    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->selectedIds = [];
        $this->idsOnPage = [];
        $this->dispatch('update-table');
    }

    // Bulk-Status-Update für mehrere Benutzer
    public function bulkUpdateStatus(string $action): void
    {
        $users = User::withTrashed()->whereIn('id', $this->selectedIds)->get();
        $count = 0;

        foreach ($users as $user) {
            $count++;

            // Verarbeitung je nach Aktion
            if ($action === 'active') {
                if ($user->trashed()) $user->restore();
                $user->update(['account_status' => AccountStatus::ACTIVE->value]);
            }
            elseif ($action === 'inactive') {
                if ($user->trashed()) $user->restore();
                $user->update(['account_status' => AccountStatus::Inactive->value]);
            }
            elseif ($action === 'archived') {
                if ($user->trashed()) $user->restore();
                $user->update(['account_status' => AccountStatus::ARCHIVED->value]);
            }
            elseif ($action === 'trash') {
                if (!$user->trashed()) $user->delete();
            }
            elseif ($action === 'restore_to_archive') {
                if ($user->trashed()) {
                    $user->restore();
                    $user->update(['account_status' => AccountStatus::ARCHIVED->value]);
                }
            }
        }

        // Erfolgsmeldung anzeigen, wenn Aktionen ausgeführt wurden
        if ($count > 0) {
            Flux::toast(
                text: __(':count employees updated successfully.', ['count' => $count]),
                heading: __('Success'),
                variant: 'success'
            );
        }

        // Auswahl zurücksetzen und UI aktualisieren
        $this->selectedIds = [];
        $this->idsOnPage = [];
        $this->dispatch('employeeUpdated');
        $this->dispatch('update-table');
    }

    // Permanentes Löschen mehrerer Benutzer aus dem Papierkorb
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

        if ($count > 0) {
            Flux::toast(
                text: __(':count employees permanently deleted.', ['count' => $count]),
                heading: __('Success.'),
                variant: 'danger'
            );
        }

        $this->selectedIds = [];
        $this->idsOnPage = [];
        $this->dispatch('employeeUpdated');
        $this->dispatch('update-table');
    }

    // Filter zurücksetzen
    public function resetFilters(): void
    {
        $this->reset('search', 'sortCol', 'sortAsc', 'statusFilter');
        $this->selectedIds = [];
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    // Benutzer in den Papierkorb verschieben
    public function delete($userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete();
            $this->dispatch('employeeUpdated');
            $this->dispatch('update-table');

            Flux::toast(
                text: __('Employee moved to trash.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }
    }

    // Benutzer auf "Nicht aktiviert" setzen
    public function notActivate($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::Inactive->value) {
            $user->update(['account_status' => AccountStatus::Inactive->value]);
            $this->dispatch('employeeUpdated');
            $this->dispatch('update-table');

            Flux::toast(
                text: __('Employee set to not activated.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }
    }

    // Benutzer archivieren
    public function archive($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::ARCHIVED->value) {
            $user->update(['account_status' => AccountStatus::ARCHIVED->value]);
            $this->dispatch('employeeUpdated');
            $this->dispatch('update-table');

            Flux::toast(
                text: __('Employee archived.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }
    }

    // Benutzer wiederherstellen (aus Archiv oder Papierkorb)
    public function restore($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if (!$user) return;

        if ($user->trashed()) {
            $user->restore();
            Flux::toast(
                text: __('Employee restored from trash.'),
                heading: __('Success.'),
                variant: 'success'
            );
        } elseif ($user->account_status === AccountStatus::ARCHIVED->value) {
            $user->update(['account_status' => AccountStatus::ACTIVE->value]);
            Flux::toast(
                text: __('Employee restored from archive.'),
                heading: __('Success.'),
                variant: 'success'
            );
        }

        $this->dispatch('employeeUpdated');
        $this->dispatch('update-table');
    }

    // Benutzer im archivierten Zustand wiederherstellen
    public function restoreToArchive($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if ($user && $user->trashed()) {
            $user->restore();
            $user->update(['account_status' => AccountStatus::ARCHIVED->value]);

            Flux::toast(
                text: __('Employee restored to archive.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('employeeUpdated');
            $this->dispatch('update-table');
        }
    }

    // Benutzer aktivieren
    public function activate($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::ACTIVE->value) {
            $user->update(['account_status' => AccountStatus::ACTIVE->value]);

            Flux::toast(
                text: __('Employee activated.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('employeeUpdated');
            $this->dispatch('update-table');
        }
    }

    // Benutzer dauerhaft löschen
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
            $this->dispatch('update-table');
        }
    }

    // Status-Filter auf die Abfrage anwenden
    protected function applyStatusFilter(Builder $query): Builder
    {
        switch ($this->statusFilter) {
            case 'inactive':
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::Inactive->value);
                break;

            case 'archived':
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ARCHIVED->value);
                break;

            case 'trash':
                $query->onlyTrashed();
                break;

            case 'active':
            default:
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ACTIVE->value);
        }

        return $query;
    }

    // Sortierung auf die Abfrage anwenden
    protected function applySorting(Builder $query): Builder
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

    // Render-Methode
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
