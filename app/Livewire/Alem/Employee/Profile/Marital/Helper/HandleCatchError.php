<?php

namespace App\Livewire\Alem\Employee\Profile\Marital\Helper;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Minimaler Trait für die Fehlerbehandlung beim Editieren
 */
trait HandleCatchError
{
    /**
     * Behandelt Fehler beim Speichern/Editieren
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
        Log::error('Fehler beim Speichern der Ehestatus-Daten', [
            // Der eigentliche Fehler
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),

            // Benutzer-Informationen
            'bearbeiteter_user_id' => $this->userId,
            'ausführender_user_id' => $this->authUserId ?? auth()->id(),
            'team_id' => $this->currentTeamId ?? null,
            'company_id' => $this->companyId ?? null,

            // Welche Felder wurden geändert
            'geänderte_felder' => $changedFields,

            // Aktuelle Formular-Werte (falls Validierungsfehler)
            'formular_daten' => [
                'civil_status' => $this->civil_status ?? null,
                'name_partner' => $this->name_partner ?? null,
                'single_parent' => $this->single_parent ?? null,
                'birthdate_partner' => $this->birthdate_partner ?? null,
                'ahv_partner' => $this->ahv_partner ?? null,
                'marriage_at' => $this->marriage_at ?? null,
            ]
        ]);

        // 4. Zeige eine benutzerfreundliche Fehlermeldung
        Flux::toast(
            text: __('Error while saving marital status data. Please try again.'),
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
}
