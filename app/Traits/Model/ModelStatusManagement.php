<?php

namespace App\Traits\Model;

use App\Enums\Model\ModelStatus;

/**
 * Trait für die Verwaltung von Model-Status.
 *
 * Stellt Methoden bereit, um den Status eines Models zu prüfen und zu ändern.
 * Interagiert mit SoftDeletes für eine korrekte Papierkorb-Funktionalität.
 */
trait ModelStatusManagement
{

    public function isActive(): bool
    {
        return $this->model_status === ModelStatus::ACTIVE && ! $this->trashed();
    }

    public function isArchived(): bool
    {
        return $this->model_status === ModelStatus::ARCHIVED && ! $this->trashed();
    }

    public function isTrashed(): bool
    {
        return $this->trashed();
    }

    public function hasStatus(ModelStatus $status): bool
    {
        return $this->model_status === $status;
    }

    public function setStatus(ModelStatus $status): self
    {
        $this->update(['model_status' => $status]);

        return $this;
    }

    public function scopeActive($query)
    {
        return $query->where('model_status', ModelStatus::ACTIVE)
            ->whereNull('deleted_at');
    }

    public function scopeArchived($query)
    {
        return $query->where('model_status', ModelStatus::ARCHIVED)
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
        $this->update(['model_status' => ModelStatus::TRASHED]);

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
        if ($this->model_status === ModelStatus::TRASHED) {
            $this->update(['model_status' => ModelStatus::ACTIVE]);
        }

        return $result;
    }

    /**
     * Stellt einen soft-deleted Benutzer wieder her und setzt den Account-Status auf den angegebenen Status.
     *
     * @param  ModelStatus  $status  Der gewünschte Account-Status (z. B. ACTIVE oder ARCHIVED).
     * @return bool True, wenn der Benutzer wiederhergestellt wurde.
     */
    public function restoreToStatus(ModelStatus $status): bool
    {
        $restored = false;
        if ($this->trashed()) {
            $this->softRestore();
            $restored = true;
        }
        $this->update(['model_status' => $status]);

        return $restored;
    }

    /**
     * Convenience-Methode: Stellt den Benutzer wieder her und setzt den Status auf ACTIVE.
     */
    public function restoreToActive(): bool
    {
        return $this->restoreToStatus(ModelStatus::ACTIVE);
    }

    /**
     * Convenience-Methode: Stellt den Benutzer wieder her und setzt den Status auf ARCHIVED.
     */
    public function restoreToArchive(): bool
    {
        return $this->restoreToStatus(ModelStatus::ARCHIVED);
    }
}
