<?php

namespace App\Livewire\Alem\Employee\Profile\Family\Helper;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Trait für die Fehlerbehandlung beim Editieren von Kindern
 */
trait HandleCatchError
{
    /**
     * Behandelt Fehler beim Speichern/Editieren von Kindern
     */
    private function handleEditingError(\Throwable $e): void
    {
        // 1. Rollback der Datenbank-Transaktion
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        // 2. Finde heraus, welche Felder geändert wurden
        $changedFields = [];
        if (method_exists($this, 'getChangedFields')) {
            try {
                $changedFields = $this->getChangedFields();
            } catch (\Throwable $ignored) {
                $changedFields = ['Unbekannt'];
            }
        }

        // 3. Logge den Fehler mit allen wichtigen Informationen
        Log::error('Fehler beim Speichern der Kinderdaten', [
            // Der eigentliche Fehler
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),

            // Identifikation
            'child_id' => $this->childId ?? null,
            'parent_user_id' => $this->userId ?? null,
            'ausführender_user_id' => $this->authUserId ?? auth()->id(),
            'team_id' => $this->currentTeamId ?? null,
            'company_id' => $this->companyId ?? null,

            // Welche Felder wurden geändert
            'geänderte_felder' => $changedFields,

            // Aktuelle Formular-Werte (falls Validierungsfehler)
            'formular_daten' => [
                'name' => $this->name ?? null,
                'gender' => $this->gender ?? null,
                'birthdate' => $this->birthdate ?? null,
                'ahv_number' => $this->ahv_number ?? null,
                'valid_until' => $this->valid_until ?? null,
            ]
        ]);

        // 4. Zeige eine benutzerfreundliche Fehlermeldung
        Flux::toast(
            text: __('Error while saving child data. Please try again.'),
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
     * Behandelt Fehler beim Erstellen von Kindern
     * (Alias für Konsistenz mit anderen Components)
     */
    private function handleSavingError(\Throwable $e): void
    {
        $this->handleEditingError($e);
    }
}
