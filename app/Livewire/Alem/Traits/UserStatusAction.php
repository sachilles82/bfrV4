<?php

namespace App\Livewire\Alem\Traits;

use App\Enums\User\AccountStatus;
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait für die Handhabung von Benutzer-Status-Aktionen in Livewire-Komponenten
 *
 * Dieser Trait stellt Methoden bereit, um Benutzer-Status des USER MODELS zu ändern und zu filtern.
 * Er kann in verschiedenen Komponenten für Employee, Partner, Lieferanten etc. verwendet werden.
 */
trait UserStatusAction
{
    /**
     * Erforderliche Eigenschaften in der Komponente:
     * public $selectedIds = [];
     * public $idsOnPage = [];
     * public $statusFilter = 'active';
     */

    /**
     * Status-Filter setzen
     *
     * @param string $status Der neue Status ('active', 'inactive', 'archived', 'trashed')
     */
    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->selectedIds = [];
        $this->idsOnPage = [];
        $this->dispatch('update-table');
    }

    /**
     * Status-Filter auf die Abfrage anwenden
     *
     * @param Builder $query Die zu filternde Query
     * @return Builder Die gefilterte Query
     */
    protected function applyStatusFilter(Builder $query): Builder
    {
        switch ($this->statusFilter) {
            case 'inactive':
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::INACTIVE);
                break;
            case 'archived':
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ARCHIVED);
                break;
            case 'trashed':
                $query->onlyTrashed();
                break;
            case 'active':
            default:
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ACTIVE);
        }
        return $query;
    }

    // Einzelne Benutzer-Statusänderungen

    /**
     * Benutzer aktivieren
     *
     * @param int|string $userId Die Benutzer-ID
     */
    public function activate($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if ($user && $user->account_status !== AccountStatus::ACTIVE) {
            $this->setUserActive($user);
            $this->showToast(__('Account activated.'));
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Benutzer auf "Inaktiv" setzen
     *
     * @param int|string $userId Die Benutzer-ID
     */
    public function notActivate($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::INACTIVE) {
            $this->setUserInactive($user);
            $this->showToast(__('Account set to inactive.'));
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Benutzer archivieren
     *
     * @param int|string $userId Die Benutzer-ID
     */
    public function archive($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::ARCHIVED) {
            $this->setUserArchived($user);
            $this->showToast(__('Account moved to archive.'));
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Benutzer in den Papierkorb verschieben
     *
     * @param int|string $userId Die Benutzer-ID
     */
    public function delete($userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $this->trashUser($user);
            $this->showToast(__('Account moved to trash.'));
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Wiederherstellung als Active.
     * Wenn der Benutzer im Papierkorb ist, wird er mit restoreToActive() wiederhergestellt.
     * Falls der Status archived oder inactive ist, wird setUserActive() aufgerufen.
     */
    public function restore($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if (!$user) return;

        if ($user->trashed()) {
            $user->restoreToActive();
            $this->showToast(__('Account restored to active.'));
        } elseif (in_array($user->account_status, [AccountStatus::ARCHIVED, AccountStatus::INACTIVE])) {
            $this->setUserActive($user);
            $this->showToast(__('Account set to active.'));
        }

        $this->resetSelections();
        $this->dispatchStatusEvents();
    }

    /**
     * Benutzer im archivierten Zustand wiederherstellen
     *
     * @param int|string $userId Die Benutzer-ID
     */
    public function restoreToArchive($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if (!$user) return;

        if ($user->trashed()) {
            $user->restoreToArchive();
            $this->showToast(__('Account restored to archive.'));
        } elseif ($user->account_status !== AccountStatus::ARCHIVED) {
            $this->setUserArchived($user);
            $this->showToast(__('Account moved to archive.'));
        }
        $this->dispatchStatusEvents();
    }

    /**
     * Benutzer im inaktiven Zustand wiederherstellen
     *
     * @param int|string $userId Die Benutzer-ID
     */
    public function restoreToInactive($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if (!$user) return;

        if ($user->trashed()) {
            $user->restoreToInactive();
            $this->showToast(__('Account restored to inactive.'));
        } elseif ($user->account_status !== AccountStatus::INACTIVE) {
            $this->setUserInactive($user);
            $this->showToast(__('Account set to inactive.'));
        }
        $this->dispatchStatusEvents();
    }

    /**
     * Benutzer dauerhaft löschen
     *
     * @param int|string $userId Die Benutzer-ID
     */
    public function forceDelete($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if ($user) {
            $user->forceDelete();
            $this->showToast(__('Account permanently deleted.'), 'Success', 'danger');
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Papierkorb leeren
     */
    public function emptyTrash(): void
    {
        $count = User::onlyTrashed()->count();
        if ($count > 0) {
            User::onlyTrashed()->forceDelete();
            $this->showToast(__(':count accounts permanently deleted.'), 'Success', 'danger', ['count' => $count]);
            $this->dispatchStatusEvents();
        } else {
            $this->showToast(__('Trash bin is already empty.'));
        }
    }

    // Bulk-Aktionen

    /**
     * Bulk-Status-Update für mehrere Benutzer
     *
     * @param string $action Die durchzuführende Aktion ('active', 'inactive', 'archived', 'trashed', 'restore_to_archive', 'restore_to_inactive')
     */
    public function bulkUpdateStatus(string $action): void
    {
        if (empty($this->selectedIds)) {
            $this->showToast(__('No accounts selected.'), 'Info', 'info');
            return;
        }

        $users = User::withTrashed()->whereIn('id', $this->selectedIds)->get();
        $count = 0;

        foreach ($users as $user) {
            $count++;
            match($action) {
                'active' => $this->setUserActive($user),
                'inactive' => $this->setUserInactive($user),
                'archived' => $this->setUserArchived($user),
                'trashed' => $this->trashUser($user),
                'restore_to_active' => $user->restoreToActive(),
                'restore_to_archive' => $user->restoreToArchive(),
                'restore_to_inactive' => $user->restoreToInactive(),
                default => null,
            };
        }

        if ($count > 0) {
            $this->showToast(__(':count accounts status updated successfully.'), 'Success', 'success', ['count' => $count]);
        }

        $this->resetSelections();
        $this->dispatchStatusEvents();
    }

    /**
     * Permanentes Löschen mehrerer Benutzer aus dem Papierkorb
     */
    public function bulkForceDelete(): void
    {
        if (empty($this->selectedIds)) {
            $this->showToast(__('No accounts selected.'), 'Info', 'info');
            return;
        }

        $users = User::withTrashed()->whereIn('id', $this->selectedIds)->get();
        $count = 0;
        foreach ($users as $user) {
            if ($user->trashed()) {
                $user->forceDelete();
                $count++;
            }
        }

        if ($count > 0) {
            $this->showToast(__(':count accounts permanently deleted.'), 'Success', 'danger', ['count' => $count]);
        }

        $this->resetSelections();
        $this->dispatchStatusEvents();
    }

    // Hilfsmethoden

    /**
     * Setzt einen Benutzer auf aktiv
     *
     * @param User $user Der zu ändernde Benutzer
     */
    private function setUserActive(User $user): void
    {
        if ($user->trashed()) {
            $user->restoreToActive();
        } else {
            $user->account_status = AccountStatus::ACTIVE;
            $user->save();
        }
    }

    /**
     * Setzt einen Benutzer auf inaktiv
     *
     * @param User $user Der zu ändernde Benutzer
     */
    private function setUserInactive(User $user): void
    {
        if ($user->trashed()) {
            $user->restoreToInactive();
        } else {
            $user->account_status = AccountStatus::INACTIVE;
            $user->save();
        }
    }

    /**
     * Archiviert einen Benutzer
     *
     * @param User $user Der zu ändernde Benutzer
     */
    private function setUserArchived(User $user): void
    {
        if ($user->trashed()) {
            $user->restoreToArchive();
        } else {
            $user->account_status = AccountStatus::ARCHIVED;
            $user->save();
        }
    }

    /**
     * Verschiebt einen Benutzer in den Papierkorb
     *
     * @param User $user Der zu ändernde Benutzer
     */
    private function trashUser(User $user): void
    {
        if (!$user->trashed()) {
            $user->delete();
        }
    }

    /**
     * Zeigt eine Toast-Nachricht an
     *
     * @param string $message Die anzuzeigende Nachricht
     * @param string $heading Die Überschrift (Standard: 'Success')
     * @param string $variant Der Typ der Nachricht (Standard: 'success')
     * @param array $params Parameter für die Übersetzung
     */
    private function showToast(string $message, string $heading = 'Success', string $variant = 'success', array $params = []): void
    {
        Flux::toast(
            text: __($message, $params),
            heading: __($heading),
            variant: $variant
        );
    }

    /**
     * Setzt ausgewählte IDs zurück
     */
    private function resetSelections(): void
    {
        $this->selectedIds = [];
        $this->idsOnPage = [];
    }

    /**
     * Sendet Events zur UI-Aktualisierung
     * Der Event-Name kann in der Komponente überschrieben werden.
     */
    private function dispatchStatusEvents(): void
    {
        // Standardmäßig "employeeUpdated" senden, kann in der Komponente angepasst werden
        $updateEvent = $this->getStatusUpdateEventName() ?? 'employeeUpdated';
        $this->dispatch($updateEvent);
        $this->dispatch('update-table');
    }

    /**
     * Gibt den Event-Namen für Status-Updates zurück
     * Kann in der Komponente überschrieben werden, um benutzerdefinierte Events zu senden
     *
     * @return string|null Der Event-Name oder null für Standardverhalten
     */
    protected function getStatusUpdateEventName(): ?string
    {
        return null; // Standard: 'employeeUpdated' (siehe dispatchStatusEvents)
    }

}
