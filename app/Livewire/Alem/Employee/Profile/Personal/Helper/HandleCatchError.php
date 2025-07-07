<?php

namespace App\Livewire\Alem\Employee\Profile\Personal\Helper;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Trait für die Fehlerbehandlung in der PersonalData Komponente.
 */
trait HandleCatchError
{
    /**
     * Generische Fehlerbehandlung für alle Operationen
     */
    private function handleError(\Throwable $e, string $operation = 'processing', array $context = []): void
    {
        // Basis-Kontext für Logging
        $logContext = [
            'exception' => $e,
            'acting_user_id' => $this->authUserId ?? auth()->id(),
            'target_user_id' => $this->userId ?? null,
            'operation' => $operation,
            'component' => 'PersonalData',
            'team_id' => $this->currentTeamId ?? null,
            'company_id' => $this->companyId ?? null
        ];

        // Füge zusätzlichen Kontext hinzu wenn vorhanden
        if (!empty($context)) {
            $logContext['context'] = $context;
        }

        // Füge Personal-spezifische Formulardaten hinzu (nur wenn verfügbar)
        if (method_exists($this, 'only')) {
            try {
                $logContext['formData'] = $this->only([
                    'birthdate',
                    'ahv_number',
                    'country_id',
                    'hometown',
                    'religion',
                    'residence_permit'
                ]);
            } catch (\Throwable $formException) {
                $logContext['formData'] = 'Unable to retrieve form data';
            }
        }

        Log::error("Fehler beim {$operation}: {$e->getMessage()}", $logContext);

        // Zeige benutzerfreundliche Meldung
        $this->showErrorToast($operation);
    }

    /**
     * Spezifische Methode für Update-Fehler bei Personal Data
     */
    private function handleEditingError(\Throwable $e): void
    {
        // Prüfe ob eine Transaktion aktiv ist bevor Rollback
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        // Sammle zusätzlichen Kontext
        $context = [
            'user_id' => $this->userId ?? null,
            'employee_exists' => isset($this->employee) && $this->employee !== null
        ];

        // Versuche geänderte Felder zu ermitteln
        try {
            if (method_exists($this, 'getChangedFields')) {
                $context['changed_fields'] = $this->getChangedFields();
            }
        } catch (\Throwable $changedFieldsException) {
            $context['changed_fields'] = 'Unable to determine changed fields';
        }

        $this->handleError($e, 'Aktualisieren der Personaldaten', $context);
    }

    /**
     * Spezifische Methode für Lade-Fehler
     */
    private function handleLoadingError(\Throwable $e): void
    {
        $context = [
            'user_id' => $this->userId ?? null
        ];

        // Prüfe ob User/Employee geladen werden konnten
        try {
            $context['user_exists'] = isset($this->user) && $this->user !== null;
            $context['employee_exists'] = isset($this->employee) && $this->employee !== null;
        } catch (\Throwable $checkException) {
            $context['entity_check_failed'] = true;
        }

        $this->handleError($e, 'Laden der Personaldaten', $context);
    }

    /**
     * Fehlerbehandlung für Validierungsfehler
     */
    private function handleValidationError(\Throwable $e): void
    {
        $context = [];

        // Versuche Validierungsfehler zu sammeln
        try {
            if (method_exists($this, 'getErrorBag')) {
                $context['validation_errors'] = $this->getErrorBag()->toArray();
            }
        } catch (\Throwable $errorBagException) {
            $context['validation_errors'] = 'Unable to retrieve validation errors';
        }

        $this->handleError($e, 'Validierung der Personaldaten', $context);
    }

    /**
     * Zeige Error Toast basierend auf Operation
     */
    private function showErrorToast(string $operation): void
    {
        $messages = [
            'Aktualisieren der Personaldaten' => [
                'text' => __('An error occurred while updating personal data. Please try again.'),
                'heading' => __('Update Error')
            ],
            'Laden der Personaldaten' => [
                'text' => __('An error occurred while loading personal data. Please refresh the page.'),
                'heading' => __('Loading Error')
            ],
            'Validierung der Personaldaten' => [
                'text' => __('Please check your input and try again.'),
                'heading' => __('Validation Error')
            ],
            'processing' => [
                'text' => __('An unexpected error occurred. Please try again.'),
                'heading' => __('Error')
            ]
        ];

        $message = $messages[$operation] ?? $messages['processing'];

        Flux::toast(
            text: $message['text'],
            heading: $message['heading'],
            variant: 'danger'
        );
    }

    /**
     * Hilfsmethode um geänderte Felder für Logging zu sammeln
     * @deprecated Use getChangedFields() from ValidatePersonalData trait instead
     */
    private function getChangedFieldsForLogging(): array
    {
        // Diese Methode ist redundant, da getChangedFields() bereits im
        // ValidatePersonalData Trait existiert
        if (method_exists($this, 'getChangedFields')) {
            return array_keys($this->getChangedFields());
        }

        return [];
    }
}
