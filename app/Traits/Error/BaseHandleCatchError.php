<?php

namespace App\Traits\Error;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Generischer Base Trait für alle Error Handler
 * Enthält die gemeinsame Logik
 */
trait BaseHandleCatchError
{
    /**
     * Generische Error Handler Methode
     *
     * @param \Throwable $e Die Exception
     * @param string $context Kontext für Logging (z.B. "Kinderdaten", "Personaldaten")
     * @param array $additionalData Zusätzliche Daten für's Logging
     */
    protected function handleError(\Throwable $e, string $context = 'Daten', array $additionalData = []): void
    {
        // 1. Rollback der Datenbank-Transaktion
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        // 2. Hole Auth-Daten direkt
        $authUser = auth()->user();

        // 3. Finde heraus, welche Felder geändert wurden
        $changedFields = $this->getChangedFieldsSafely();

        // 4. Sammle Identifikations-Daten
        $identificationData = $this->collectIdentificationData();

        // 5. Sammle Formular-Daten
        $formData = $this->collectFormData();

        // 6. Merge alle Daten zusammen
        $logData = array_merge([
            // Der eigentliche Fehler
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'error_class' => get_class($e),

            // Auth Info
            'ausführender_user_id' => $authUser?->id,
            'team_id' => $authUser?->current_team_id,
            'company_id' => $authUser?->company_id,

            // Component Info
            'component' => get_class($this),

            // Daten
            'geänderte_felder' => $changedFields,
            'formular_daten' => $formData,
        ], $identificationData, $additionalData);

        // 7. Logge den Fehler
        Log::error("Fehler beim Speichern der {$context}", $logData);

        // 8. Zeige eine benutzerfreundliche Fehlermeldung
        Flux::toast(
            text: __("Error while saving {$context}. Please try again."),
            heading: __('Saving Error'),
            variant: 'danger'
        );

        // Optional: In Development-Umgebung zeige mehr Details
        if (config('app.debug')) {
            Flux::toast(
                text: 'Debug: ' . $e->getMessage(),
                heading: 'Fehler-Details',
                variant: 'warning'
            );
        }
    }

    /**
     * Sicheres Abrufen der geänderten Felder
     */
    protected function getChangedFieldsSafely(): array
    {
        if (method_exists($this, 'getChangedFields')) {
            try {
                return $this->getChangedFields();
            } catch (\Throwable $ignored) {
                // Ignoriere Fehler beim Abrufen der geänderten Felder
            }
        }
        return ['Unbekannt'];
    }

    /**
     * Abstrakte Methode - muss von spezifischen Traits implementiert werden
     * @return array
     */
    abstract protected function collectIdentificationData(): array;

    /**
     * Abstrakte Methode - muss von spezifischen Traits implementiert werden
     * @return array
     */
    abstract protected function collectFormData(): array;
}
