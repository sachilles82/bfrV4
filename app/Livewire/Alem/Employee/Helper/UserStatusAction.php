<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Enums\User\AccountStatus;
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;

trait UserStatusAction
{

    // Status-Filter Methoden

    /**
     * Status-Filter setzen
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
     */
    protected function applyStatusFilter(Builder $query): Builder
    {
        switch ($this->statusFilter) {
            case 'inactive':
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::INACTIVE->value);
                break;

            case 'archived':
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ARCHIVED->value);
                break;

            case 'trashed':
                $query->onlyTrashed();
                break;

            case 'active':
            default:
                $query->whereNull('deleted_at')
                    ->where('account_status', AccountStatus::ACTIVE->value);
        }

        return $query;
    }

    // Einzelne Benutzer-Statusänderungen

    /**
     * Benutzer aktivieren
     */
    public function activate($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::ACTIVE->value) {
            $this->setUserActive($user);
            $this->showToast('Employee activated.');
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Benutzer auf "Inaktiv" setzen
     */
    public function notActivate($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::INACTIVE->value) {
            $this->setUserInactive($user);
            $this->showToast('Employee set to inactive.');
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Benutzer archivieren
     */
    public function archive($userId): void
    {
        $user = User::find($userId);
        if ($user && $user->account_status !== AccountStatus::ARCHIVED->value) {
            $this->setUserArchived($user);
            $this->showToast('Employee archived.');
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Benutzer in den Papierkorb verschieben
     */
    public function delete($userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $this->trashUser($user);
            $this->showToast('Employee moved to trash.');
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Benutzer wiederherstellen (aus Archiv oder Papierkorb)
     */
    public function restore($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if (!$user) return;

        if ($user->trashed()) {
            $user->restore();
            $this->showToast('Employee restored from trash.');
        } elseif ($user->account_status === AccountStatus::ARCHIVED->value) {
            $this->setUserActive($user);
            $this->showToast('Employee restored from archive.');
        }

        $this->dispatchStatusEvents();
    }

    /**
     * Benutzer im archivierten Zustand wiederherstellen
     */
    public function restoreToArchive($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if ($user && $user->trashed()) {
            $this->restoreUserToArchive($user);
            $this->showToast('Employee restored to archive.');
            $this->dispatchStatusEvents();
        }
    }

    /**
     * Benutzer dauerhaft löschen
     */
    public function forceDelete($userId): void
    {
        $user = User::withTrashed()->find($userId);
        if ($user) {
            $user->forceDelete();
            $this->showToast('Employee permanently deleted.', 'Success.', 'danger');
            $this->dispatchStatusEvents();
        }
    }

    // Bulk-Aktionen

    /**
     * Bulk-Status-Update für mehrere Benutzer
     */
    public function bulkUpdateStatus(string $action): void
    {
        $users = User::withTrashed()->whereIn('id', $this->selectedIds)->get();
        $count = 0;

        foreach ($users as $user) {
            $count++;

            match($action) {
                'active' => $this->setUserActive($user),
                'inactive' => $this->setUserInactive($user),
                'archived' => $this->setUserArchived($user),
                'trashed' => $this->trashUser($user),
                'restore_to_archive' => $this->restoreUserToArchive($user),
                default => null,
            };
        }

        if ($count > 0) {
            $this->showToast(
                ':count employees updated successfully.',
                'Success',
                'success',
                ['count' => $count]
            );
        }

        $this->resetSelections();
        $this->dispatchStatusEvents();
    }

    /**
     * Permanentes Löschen mehrerer Benutzer aus dem Papierkorb
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

        if ($count > 0) {
            $this->showToast(
                ':count employees permanently deleted.',
                'Success.',
                'danger',
                ['count' => $count]
            );
        }

        $this->resetSelections();
        $this->dispatchStatusEvents();
    }

    // Hilfsmethoden

    /**
     * Setzt einen Benutzer auf aktiv
     */
    private function setUserActive(User $user): void
    {
        if ($user->trashed()) $user->restore();
        $user->update(['account_status' => AccountStatus::ACTIVE->value]);
    }

    /**
     * Setzt einen Benutzer auf inaktiv
     */
    private function setUserInactive(User $user): void
    {
        if ($user->trashed()) $user->restore();
        $user->update(['account_status' => AccountStatus::INACTIVE->value]);
    }

    /**
     * Archiviert einen Benutzer
     */
    private function setUserArchived(User $user): void
    {
        if ($user->trashed()) $user->restore();
        $user->update(['account_status' => AccountStatus::ARCHIVED->value]);
    }

    /**
     * Verschiebt einen Benutzer in den Papierkorb
     */
    private function trashUser(User $user): void
    {
        if (!$user->trashed()) $user->delete();
    }

    /**
     * Stellt einen Benutzer im archivierten Zustand wieder her
     */
    private function restoreUserToArchive(User $user): void
    {
        if ($user->trashed()) {
            $user->restore();
            $user->update(['account_status' => AccountStatus::ARCHIVED->value]);
        }
    }

    /**
     * Zeigt eine Toast-Nachricht an
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
     */
    private function dispatchStatusEvents(): void
    {
        $this->dispatch('employeeUpdated');
        $this->dispatch('update-table');
    }

}
