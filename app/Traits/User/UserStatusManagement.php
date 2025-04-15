<?php

namespace App\Traits\User;

use App\Enums\User\AccountStatus;

/**
 * Trait für die Verwaltung von Benutzer-Status.
 *
 * Stellt Methoden bereit, um den Status eines Benutzers zu prüfen und zu ändern.
 * Interagiert mit SoftDeletes für eine korrekte Papierkorb-Funktionalität.
 */
trait UserStatusManagement
{
    // --- Status-Checks und Scopes (unverändert) ---

    public function isActive(): bool
    {
        return $this->account_status === AccountStatus::ACTIVE && ! $this->trashed();
    }

    public function isNotActivated(): bool
    {
        return $this->account_status === AccountStatus::INACTIVE && ! $this->trashed();
    }

    public function isArchived(): bool
    {
        return $this->account_status === AccountStatus::ARCHIVED && ! $this->trashed();
    }

    public function isTrashed(): bool
    {
        return $this->trashed();
    }

    public function hasStatus(AccountStatus $status): bool
    {
        return $this->account_status === $status;
    }

    public function setStatus(AccountStatus $status): self
    {
        $this->update(['account_status' => $status]);

        return $this;
    }

    public function scopeActive($query)
    {
        return $query->where('account_status', AccountStatus::ACTIVE)
            ->whereNull('deleted_at');
    }

    public function scopeNotActivated($query)
    {
        return $query->where('account_status', AccountStatus::INACTIVE)
            ->whereNull('deleted_at');
    }

    public function scopeArchived($query)
    {
        return $query->where('account_status', AccountStatus::ARCHIVED)
            ->whereNull('deleted_at');
    }

    public function scopeNotTrashed($query)
    {
        return $query->whereNull('deleted_at');
    }

    // --- Überschreiben von delete und restore ---

    /**
     * Überschreibt die delete()-Methode des SoftDeletes-Traits.
     * Setzt den Account-Status auf TRASHED vor dem Soft-Delete.
     *
     * @return bool|null
     */
    public function delete()
    {
        $this->update(['account_status' => AccountStatus::TRASHED]);

        return parent::delete();
    }

    /**
     * Überschreibt die restore()-Methode des SoftDeletes-Traits.
     * Führt zuerst die SoftDeletes-Logik aus und setzt dann (falls nötig) den Status auf ACTIVE.
     *
     * @return bool|null
     */
    public function restore()
    {
        // Rufe die originale restore()-Methode von SoftDeletes über den Alias auf.
        $result = $this->softRestore();

        // Falls der Account-Status auf TRASHED stand, setze ihn auf ACTIVE.
        if ($this->account_status === AccountStatus::TRASHED) {
            $this->update(['account_status' => AccountStatus::ACTIVE]);
        }

        return $result;
    }

    /**
     * Stellt einen soft-deleted Benutzer wieder her und setzt den Account-Status auf den angegebenen Status.
     *
     * @param  AccountStatus  $status  Der gewünschte Account-Status (z. B. INACTIVE oder ARCHIVED).
     * @return bool True, wenn der Benutzer wiederhergestellt wurde.
     */
    public function restoreToStatus(AccountStatus $status): bool
    {
        $restored = false;
        if ($this->trashed()) {
            $this->softRestore();
            $restored = true;
        }
        $this->update(['account_status' => $status]);

        return $restored;
    }

    /**
     * Convenience-Methode: Stellt den Benutzer wieder her und setzt den Status auf ACTIVE.
     */
    public function restoreToActive(): bool
    {
        return $this->restoreToStatus(AccountStatus::ACTIVE);
    }

    /**
     * Convenience-Methode: Stellt den Benutzer wieder her und setzt den Status auf INACTIVE.
     */
    public function restoreToInactive(): bool
    {
        return $this->restoreToStatus(AccountStatus::INACTIVE);
    }

    /**
     * Convenience-Methode: Stellt den Benutzer wieder her und setzt den Status auf ARCHIVED.
     */
    public function restoreToArchive(): bool
    {
        return $this->restoreToStatus(AccountStatus::ARCHIVED);
    }
}
