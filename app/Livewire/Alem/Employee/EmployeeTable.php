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

    // Filter: active, not_activated, archived, trash
    public $statusFilter = 'active';

    /**
     * Setzt den Status-Filter (z. B. beim Klick auf einen Wert in der Tabelle)
     */
    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetSelections();
        $this->dispatch('update-table');
    }

    /**
     * Führt eine Bulk-Aktion auf alle ausgewählten Mitarbeiter aus.
     */
    public function bulkUpdateStatus(string $action): void
    {
        // Use withTrashed() to include trashed users in the query
        $users = $this->getSelectedUsers();
        $count = 0;

        foreach ($users as $user) {
            $count++;
            $this->processUserAction($user, $action);
        }

        $this->showSuccessMessage($count);
        $this->resetSelections();
        $this->notifyUiUpdate();
    }

    /**
     * Verarbeitet eine Benutzeraktion basierend auf dem Aktionstyp
     */
    private function processUserAction(User $user, string $action): void
    {
        switch ($action) {
            case 'active':
                $this->restoreIfTrashed($user);
                $this->updateUserStatus($user, AccountStatus::ACTIVE->value);
                break;

            case 'not_activated':
                $this->restoreIfTrashed($user);
                $this->updateUserStatus($user, AccountStatus::NOT_ACTIVATED->value);
                break;

            case 'archived':
                $this->restoreIfTrashed($user);
                $this->updateUserStatus($user, AccountStatus::ARCHIVED->value);
                break;

            case 'trash':
                if (!$user->trashed()) {
                    $user->delete(); // This will also set account_status to 'trashed'
                }
                break;

            case 'restore_to_archive':
                if ($user->trashed()) {
                    $user->restore();
                    $this->updateUserStatus($user, AccountStatus::ARCHIVED->value);
                }
                break;
        }
    }

    /**
     * Stellt einen Benutzer wieder her, wenn er im Papierkorb ist
     */
    private function restoreIfTrashed(User $user): void
    {
        if ($user->trashed()) {
            $user->restore();
        }
    }

    /**
     * Aktualisiert den Account-Status eines Benutzers
     */
    private function updateUserStatus(User $user, string $status): void
    {
        $user->update([
            'account_status' => $status
        ]);
    }

    /**
     * Zeigt eine Erfolgsmeldung an, wenn Aktionen ausgeführt wurden
     */
    private function showSuccessMessage(int $count): void
    {
        if ($count > 0) {
            Flux::toast(
                text: __(':count employees updated successfully.', ['count' => $count]),
                heading: __('Success'),
                variant: 'success'
            );
        }
    }

    /**
     * Holt alle ausgewählten Benutzer inkl. gelöschter
     */
    private function getSelectedUsers()
    {
        return User::withTrashed()->whereIn('id', $this->selectedIds)->get();
    }

    /**
     * Permanently deletes multiple employees from trash.
     */
    public function bulkForceDelete(): void
    {
        $users = $this->getSelectedUsers();
        $count = $this->permanentlyDeleteUsers($users);

        if ($count > 0) {
            $this->showDeleteSuccessMessage($count);
        }

        $this->resetSelections();
        $this->notifyUiUpdate();
    }

    /**
     * Löscht Benutzer permanent und gibt die Anzahl zurück
     */
    private function permanentlyDeleteUsers($users): int
    {
        $count = 0;
        foreach ($users as $user) {
            if ($user->trashed()) {
                $user->forceDelete();
                $count++;
            }
        }
        return $count;
    }

    /**
     * Zeigt eine Erfolgsmeldung für dauerhafte Löschung an
     */
    private function showDeleteSuccessMessage(int $count): void
    {
        Flux::toast(
            text: __(':count employees permanently deleted.', ['count' => $count]),
            heading: __('Success.'),
            variant: 'danger'
        );
    }

    /**
     * Setzt Filter zurück.
     */
    public function resetFilters(): void
    {
        $this->reset('search', 'sortCol', 'sortAsc', 'statusFilter');
        $this->resetSelections();
        $this->dispatch('resetFilters');
        $this->dispatch('update-table');
    }

    /**
     * Setzt die Auswahlfelder zurück
     */
    private function resetSelections(): void
    {
        $this->selectedIds = [];
        $this->idsOnPage = [];
    }

    /**
     * Benachrichtigt die UI über Aktualisierungen
     */
    private function notifyUiUpdate(): void
    {
        $this->dispatch('employeeUpdated');
        $this->dispatch('update-table');
    }

    /**
     * Soft-deletes einen Mitarbeiter.
     */
    public function delete($userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $user->delete(); // This will also set account_status to 'trashed'
            $this->notifyUiUpdate();
            $this->showToast('Employee moved to trash.', 'Success.', 'success');
        }
    }

    /**
     * Setzt den Benutzer auf "Nicht aktiviert"
     */
    public function notActivate($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::NOT_ACTIVATED->value) {
            $this->updateUserStatus($user, AccountStatus::NOT_ACTIVATED->value);
            $this->notifyUiUpdate();
            $this->showToast('Employee set to not activated.', 'Success.', 'success');
        }
    }

    /**
     * Archiviert einen Mitarbeiter (setzt account_status auf 'archived').
     */
    public function archive($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::ARCHIVED->value) {
            $this->updateUserStatus($user, AccountStatus::ARCHIVED->value);
            $this->notifyUiUpdate();
            $this->showToast('Employee archived.', 'Success.', 'success');
        }
    }

    /**
     * Stellt einen archivierten oder gelöschten Mitarbeiter wieder her.
     */
    public function restore($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if (!$user) return;

        if ($user->trashed()) {
            $user->restore(); // This will also restore account_status
            $this->showToast('Employee restored from trash.', 'Success.', 'success');
        } elseif ($user->account_status === AccountStatus::ARCHIVED->value) {
            $this->updateUserStatus($user, AccountStatus::ACTIVE->value);
            $this->showToast('Employee restored from archive.', 'Success.', 'success');
        }

        $this->notifyUiUpdate();
    }

    /**
     * Stellt einen gelöschten Mitarbeiter im archivierten Zustand wieder her
     */
    public function restoreToArchive($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if ($user && $user->trashed()) {
            $user->restore();  // First restore from trash
            $this->updateUserStatus($user, AccountStatus::ARCHIVED->value);
            $this->showToast('Employee restored to archive.', 'Success.', 'success');
            $this->notifyUiUpdate();
        }
    }

    /**
     * Aktiviert einen nicht aktivierten Mitarbeiter.
     */
    public function activate($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::ACTIVE->value) {
            $this->updateUserStatus($user, AccountStatus::ACTIVE->value);
            $this->showToast('Employee activated.', 'Success.', 'success');
            $this->notifyUiUpdate();
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
            $this->showToast('Employee permanently deleted.', 'Success.', 'danger');
            $this->notifyUiUpdate();
        }
    }

    /**
     * Zeigt einen Toast an
     */
    private function showToast(string $text, string $heading, string $variant): void
    {
        Flux::toast(
            text: __($text),
            heading: __($heading),
            variant: $variant
        );
    }

    /**
     * Wendet den Status-Filter an.
     */
    protected function applyStatusFilter(Builder $query): Builder
    {
        switch ($this->statusFilter) {
            case 'not_activated':
                // Explicitly filter for not activated, non-trashed users
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::NOT_ACTIVATED->value);
                break;

            case 'archived':
                // Explicitly filter for archived, non-trashed users
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ARCHIVED->value);
                break;

            case 'trash':
                // Only show trashed users, regardless of their account_status
                $query->onlyTrashed();
                break;

            case 'active':
            default:
                // Explicitly filter for active, non-trashed users
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ACTIVE->value);
        }

        return $query;
    }

    /**
     * Wendet die Sortierung auf die Abfrage an
     */
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
