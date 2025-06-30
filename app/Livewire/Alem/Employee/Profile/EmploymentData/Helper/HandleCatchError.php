<?php

namespace App\Livewire\Alem\Employee\Profile\EmploymentData\Helper;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Trait für die Fehlerbehandlung in der EmploymentData Komponente.
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
            'component' => 'EmploymentData'
        ];

        // Füge zusätzlichen Kontext hinzu wenn vorhanden
        if (!empty($context)) {
            $logContext['context'] = $context;
        }

        // Füge Employment-spezifische Formulardaten hinzu
        if (method_exists($this, 'only')) {
            $logContext['formData'] = $this->only([
                'ahv_number',
                'nationality',
                'hometown',
                'religion',
                'civil_status',
                'residence_permit'
            ]);
        }

        Log::error("Fehler beim {$operation}: {$e->getMessage()}", $logContext);

        // Zeige benutzerfreundliche Meldung
        $this->showErrorToast($operation);
    }

    /**
     * Spezifische Methode für Update-Fehler bei Employment Data
     * @throws \Throwable
     */
    private function handleEditingError(\Throwable $e): void
    {
        // Prüfe ob eine Transaktion aktiv ist bevor Rollback
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $this->handleError($e, 'Aktualisieren der Beschäftigungsdaten', [
            'employee_id' => $this->employee?->id,
            'changed_fields' => $this->getChangedFieldsForLogging()
        ]);
    }

    /**
     * Spezifische Methode für Lade-Fehler
     */
    private function handleLoadingError(\Throwable $e): void
    {
        $this->handleError($e, 'Laden der Beschäftigungsdaten', [
            'employee_exists' => $this->employee !== null
        ]);
    }

    /**
     * Fehlerbehandlung für Validierungsfehler
     */
    private function handleValidationError(\Throwable $e): void
    {
        $this->handleError($e, 'Validierung der Beschäftigungsdaten', [
            'validation_errors' => $this->getErrorBag()->toArray()
        ]);
    }

    /**
     * Zeige Error Toast basierend auf Operation
     */
    private function showErrorToast(string $operation): void
    {
        $messages = [
            'Aktualisieren der Beschäftigungsdaten' => [
                'text' => __('An error occurred while updating employment data.'),
                'heading' => __('Update Error')
            ],
            'Laden der Beschäftigungsdaten' => [
                'text' => __('An error occurred while loading employment data.'),
                'heading' => __('Loading Error')
            ],
            'Validierung der Beschäftigungsdaten' => [
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
            'ahv_number' => 'ahvNumberHasChanged',
            'nationality' => 'nationalityHasChanged',
            'hometown' => 'hometownHasChanged',
            'religion' => 'religionHasChanged',
            'civil_status' => 'civilStatusHasChanged',
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
