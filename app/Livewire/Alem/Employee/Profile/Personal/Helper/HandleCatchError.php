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
            'target_user_id' => $this->userId,
            'operation' => $operation,
            'component' => 'PersonalData'
        ];

        // Füge zusätzlichen Kontext hinzu wenn vorhanden
        if (!empty($context)) {
            $logContext['context'] = $context;
        }

        // Füge Personal-spezifische Formulardaten hinzu
        if (method_exists($this, 'only')) {
            $logContext['formData'] = $this->only([
                'birthdate',
                'ahv_number',
                'country_id',
                'hometown',
                'religion',
                'residence_permit'
            ]);
        }

        Log::error("Fehler beim {$operation}: {$e->getMessage()}", $logContext);

        // Zeige benutzerfreundliche Meldung
        $this->showErrorToast($operation);
    }

    /**
     * Spezifische Methode für Update-Fehler bei Personal Data
     * @throws \Throwable
     */
    private function handleEditingError(\Throwable $e): void
    {
        // Prüfe ob eine Transaktion aktiv ist bevor Rollback
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $this->handleError($e, 'Aktualisieren der Personaldaten', [
            'user_id' => $this->user?->id,
            'employee_id' => $this->employee?->id,
            'changed_fields' => $this->getChangedFieldsForLogging()
        ]);
    }

    /**
     * Spezifische Methode für Lade-Fehler
     */
    private function handleLoadingError(\Throwable $e): void
    {
        $this->handleError($e, 'Laden der Personaldaten', [
            'user_exists' => $this->user !== null,
            'employee_exists' => $this->employee !== null
        ]);
    }

    /**
     * Fehlerbehandlung für Validierungsfehler
     */
    private function handleValidationError(\Throwable $e): void
    {
        $this->handleError($e, 'Validierung der Personaldaten', [
            'validation_errors' => $this->getErrorBag()->toArray()
        ]);
    }

    /**
     * Zeige Error Toast basierend auf Operation
     */
    private function showErrorToast(string $operation): void
    {
        $messages = [
            'Aktualisieren der Personaldaten' => [
                'text' => __('An error occurred while updating personal data.'),
                'heading' => __('Update Error')
            ],
            'Laden der Personaldaten' => [
                'text' => __('An error occurred while loading personal data.'),
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
     */
    private function getChangedFieldsForLogging(): array
    {
        $changedFields = [];

        $fields = [
            'birthdate' => 'birthdateHasChanged',
            'ahv_number' => 'ahvNumberHasChanged',
            'country_id' => 'countryIdHasChanged',
            'hometown' => 'hometownHasChanged',
            'religion' => 'religionHasChanged',
            'residence_permit' => 'residencePermitHasChanged'
        ];

        foreach ($fields as $field => $method) {
            if (method_exists($this, $method) && $this->$method()) {
                $changedFields[] = $field;
            }
        }

        return $changedFields;
    }
}
