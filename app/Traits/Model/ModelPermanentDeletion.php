<?php

namespace App\Traits\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Prunable;

trait ModelPermanentDeletion
{
    use Prunable;

    /**
     * Konstante für die Anzahl der Tage bis zur permanenten Löschung
     */
    public static function getPermanentDeleteDays(): int
    {
        return 7;
    }

    /**
     * Überschreibe die prunable Methode, um genau die definierte Anzahl an Tagen zu warten
     */
    public function prunable()
    {
        return static::onlyTrashed()
            ->where('deleted_at', '<=', now()->subDays(static::getPermanentDeleteDays()));
    }

    /**
     * Berechnet die verbleibenden Tage bis zur permanenten Löschung
     */
    public function getDaysUntilPermanentDeleteAttribute(): ?int
    {
        if (!$this->trashed()) {
            return null;
        }

        // Berechne einfach die Differenz in Tagen vom Löschdatum bis heute
        $daysSinceDeletion = $this->deleted_at->startOfDay()->diffInDays(now()->startOfDay());

        // Verbleibende Tage = Gesamte Tage minus bereits vergangene Tage
        $daysLeft = static::getPermanentDeleteDays() - $daysSinceDeletion;

        // Nie weniger als 0 Tage zurückgeben
        return max(0, $daysLeft);
    }

    /**
     * Gibt das Datum der permanenten Löschung zurück
     */
    public function getPermanentDeletionDateAttribute(): ?Carbon
    {
        if (!$this->trashed()) {
            return null;
        }

        // Einfach die definierte Anzahl von Tagen zum Löschdatum addieren
        return $this->deleted_at->copy()->addDays(static::getPermanentDeleteDays());
    }

    /**
     * Gibt eine benutzerfreundliche Nachricht zurück, wann der Benutzer gelöscht wird
     */
    public function getDeletionMessageAttribute(): ?string
    {
        if (!$this->trashed()) {
            return null;
        }

        if ($this->days_until_permanent_delete <= 0) {
            return __('Will be deleted soon');
        }

        if ($this->days_until_permanent_delete === 1) {
            return __('Will be deleted tomorrow');
        }

        return __('Will be deleted in :days days', ['days' => $this->days_until_permanent_delete]);
    }

    /**
     * Gibt das Löschdatum in benutzerfreundlichem Format zurück
     */
    public function getPermanentDeletionDateForHumansAttribute(): ?string
    {
        if (!$this->permanent_deletion_date) {
            return null;
        }

        return $this->permanent_deletion_date->format(
            $this->permanent_deletion_date->year === now()->year
                ? 'M d, g:i A'  // Ohne Jahresangabe, wenn im aktuellen Jahr
                : 'M d, Y, g:i A'  // Mit Jahresangabe, wenn in einem anderen Jahr
        );
    }

    /**
     * Gibt eine CSS-Klasse basierend auf der Dringlichkeit der Löschung zurück
     */
    public function getDeletionUrgencyClassAttribute(): ?string
    {
        if (!$this->trashed()) {
            return null;
        }

        if ($this->days_until_permanent_delete <= 1) {
            return 'text-red-600 dark:text-red-400 font-medium';
        }

        if ($this->days_until_permanent_delete <= 3) {
            return 'text-amber-600 dark:text-amber-400';
        }

        return 'text-gray-600 dark:text-gray-400';
    }
}
