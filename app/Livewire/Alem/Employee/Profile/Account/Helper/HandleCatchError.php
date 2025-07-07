<?php

namespace App\Livewire\Alem\Employee\Profile\Account\Helper;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Minimaler Trait für die Fehlerbehandlung beim Editieren von Account Details
 */
trait HandleCatchError
{
    /**
     * Behandelt Fehler beim Speichern/Editieren der Account Details
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
        Log::error('Fehler beim Speichern der Account Details', [
            // Der eigentliche Fehler
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'error_trace' => $e->getTraceAsString(),

            // Benutzer-Informationen
            'bearbeiteter_user_id' => $this->userId,
            'ausführender_user_id' => $this->authUserId ?? auth()->id(),
            'team_id' => $this->currentTeamId ?? null,
            'company_id' => $this->companyId ?? null,

            // Welche Felder wurden geändert
            'geänderte_felder' => $changedFields,

            // Aktuelle Formular-Werte (für Debugging)
            'formular_daten' => [
                'gender' => $this->gender ?? null,
                'name' => $this->name ?? null,
                'email' => $this->email ?? null,
                'phone_1' => $this->phone_1 ?? null,
                'phone_2' => $this->phone_2 ?? null,
                'model_status' => $this->model_status ?? null,
            ],

            // Original-Daten zum Vergleich
            'original_daten' => $this->originalData ?? []
        ]);

        // 4. Zeige eine benutzerfreundliche Fehlermeldung
        Flux::toast(
            text: __('Error while saving account details. Please try again.'),
            heading: __('Saving Error'),
            variant: 'danger'
        );

        // 5. Optional: In Development-Umgebung zeige mehr Details
        if (config('app.debug')) {
            $errorDetail = match (true) {
                str_contains($e->getMessage(), 'email') => 'Email-Validierung fehlgeschlagen',
                str_contains($e->getMessage(), 'unique') => 'Doppelter Eintrag gefunden',
                str_contains($e->getMessage(), 'phone') => 'Telefonnummer-Format ungültig',
                default => substr($e->getMessage(), 0, 100)
            };

            Flux::toast(
                text: 'Debug: ' . $errorDetail,
                heading: 'Fehler-Details',
                variant: 'warning'
            );
        }
    }

    /**
     * Behandelt Fehler beim Laden der Account Details
     */
    private function handleLoadingError(\Throwable $e): void
    {
        // Logge den Fehler
        Log::error('Fehler beim Laden der Account Details', [
            'error_message' => $e->getMessage(),
            'user_id' => $this->userId ?? null,
            'ausführender_user_id' => $this->authUserId ?? auth()->id(),
        ]);

        // Zeige benutzerfreundliche Fehlermeldung
        Flux::toast(
            text: __('Could not load account details. Please refresh the page.'),
            heading: __('Loading Error'),
            variant: 'danger'
        );
    }

    /**
     * Behandelt Validierungsfehler mit spezifischen Meldungen
     */
    private function handleValidationError(array $failedRules): void
    {
        // Logge Validierungsfehler
        Log::warning('Validierung der Account Details fehlgeschlagen', [
            'user_id' => $this->userId,
            'fehlgeschlagene_regeln' => $failedRules,
            'formular_daten' => [
                'gender' => $this->gender ?? null,
                'name' => $this->name ?? null,
                'email' => $this->email ?? null,
                'phone_1' => $this->phone_1 ?? null,
                'phone_2' => $this->phone_2 ?? null,
                'model_status' => $this->model_status ?? null,
            ]
        ]);

        // Zeige spezifische Fehlermeldung
        $fieldNames = [
            'gender' => __('Gender'),
            'name' => __('Name'),
            'email' => __('Email'),
            'phone_1' => __('Primary Phone'),
            'phone_2' => __('Secondary Phone'),
            'model_status' => __('Account Status')
        ];

        $failedFieldNames = array_map(
            fn($field) => $fieldNames[$field] ?? $field,
            array_keys($failedRules)
        );

        Flux::toast(
            text: __('Please check the following fields: :fields', [
                'fields' => implode(', ', $failedFieldNames)
            ]),
            heading: __('Validation Error'),
            variant: 'warning'
        );
    }
}
