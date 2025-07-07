<?php

namespace App\Livewire\Alem\Employee\Profile\Employment\Helper;

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
        Log::error('Fehler beim Speichern der Anstellungsdaten', [
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
                'joined_at' => $this->joined_at ?? null,
                'personal_number' => $this->personal_number ?? null,
                'prob_period' => $this->prob_period ?? null,
                'probation_at' => $this->probation_at ?? null,
                'notice_at' => $this->notice_at ?? null,
                'notice_period' => $this->notice_period ?? null,
                'leave_at' => $this->leave_at ?? null,
            ]
        ]);

        // 4. Zeige eine benutzerfreundliche Fehlermeldung
        Flux::toast(
            text: __('Fehler beim Speichern. Bitte versuchen Sie es erneut.'),
            heading: __('Speicherfehler'),
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
