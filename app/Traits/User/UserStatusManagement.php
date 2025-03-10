<?php

namespace App\Traits\User;

use App\Enums\User\AccountStatus;

trait UserStatusManagement
{
    /* Prüft, ob der Benutzer aktiv ist */
    public function isActive(): bool
    {
        return $this->account_status === AccountStatus::ACTIVE
            && !$this->trashed();
    }

    /* Prüft, ob der Benutzer nicht aktiviert ist */
    public function isNotActivated(): bool
    {
        return $this->account_status === AccountStatus::INACTIVE
            && !$this->trashed();
    }

    /* Prüft, ob der Benutzer archiviert ist */
    public function isArchived(): bool
    {
        return $this->account_status === AccountStatus::ARCHIVED
            && !$this->trashed();
    }

    /* Prüft, ob der Benutzer im Papierkorb ist */
    public function isTrashed(): bool
    {
        return $this->trashed();
    }

    /* Prüft, ob der Benutzer in einem bestimmten Status ist */
    public function hasStatus(AccountStatus $status): bool
    {
        return $this->account_status === $status;
    }

    /* Setzt den Status des Benutzers */
    public function setStatus(AccountStatus $status): self
    {
        $this->update(['account_status' => $status]);
        return $this;
    }

    /* Scope for active users */
    public function scopeActive($query)
    {
        return $query->where('account_status', AccountStatus::ACTIVE->value)
            ->whereNull('deleted_at');
    }

    /* Scope for not activated users */
    public function scopeNotActivated($query)
    {
        return $query->where('account_status', AccountStatus::INACTIVE->value)
            ->whereNull('deleted_at');
    }

    /* Scope for archived users */
    public function scopeArchived($query)
    {
        return $query->where('account_status', AccountStatus::ARCHIVED->value)
            ->whereNull('deleted_at');
    }

    /* Scope for all non-deleted users */
    public function scopeNotTrashed($query)
    {
        return $query->whereNull('deleted_at');
    }

    /* Override the SoftDeletes trait delete method to update account_status */
    public function delete()
    {
        $this->update(['account_status' => AccountStatus::TRASHED->value]);
        return parent::delete();
    }

    /* Override the SoftDeletes trait's restore method to restore previous status */
    public function restore()
    {
        $result = parent::restore();
        // If account_status is trashed, change it back to active
        if ($this->account_status === AccountStatus::TRASHED) {
            $this->update(['account_status' => AccountStatus::ACTIVE->value]);
        }
        return $result;
    }
}
