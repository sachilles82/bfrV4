<?php

namespace App\Livewire\Alem\Employee\Helper\Secure;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Trait für die Fehlerbehandlung in den Employee Komponenten.
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
            'operation' => $operation,
        ];

        // Füge zusätzlichen Kontext hinzu wenn vorhanden
        if (!empty($context)) {
            $logContext['context'] = $context;
        }

        // Füge Formulardaten hinzu wenn verfügbar
        if (method_exists($this, 'only')) {
            $logContext['formData'] = $this->only([
                'gender',
                'name',
                'email',
                'phone_1',
                'phone_2',
                'model_status',
                'joined_at',
                'invitation'
            ]);
        }

        Log::error("Fehler beim {$operation}: {$e->getMessage()}", $logContext);

        // Zeige benutzerfreundliche Meldung
        $this->showErrorToast($operation);
    }

    /**
     * Spezifische Methode für Speicher-Fehler (Create)
     */
    private function handleSavingError(\Throwable $e): void
    {
        $this->handleError($e, 'Erstellen des Mitarbeiters');
    }

    /**
     * Spezifische Methode für Update-Fehler
     * @throws \Throwable
     */
    private function handleEditingError(\Throwable $e): void
    {
        // Prüfe ob eine Transaktion aktiv ist bevor Rollback
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        $this->handleError($e, 'Aktualisieren des Mitarbeiters');
    }

    /**
     * Spezifische Methode für Lade-Fehler
     */
    private function handleLoadingError(\Throwable $e): void
    {
        $this->handleError($e, 'Laden der Relationsdaten');
    }

    /**
     * Zeige Error Toast basierend auf Operation
     */
    private function showErrorToast(string $operation): void
    {
        $messages = [
            'Erstellen des Mitarbeiters' => [
                'text' => __('An error occurred while creating the employee.'),
                'heading' => __('Creation Error')
            ],
            'Aktualisieren des Mitarbeiters' => [
                'text' => __('An error occurred while updating the employee.'),
                'heading' => __('Update Error')
            ],
            'Laden der Relationsdaten' => [
                'text' => __('An error occurred while loading the relation data.'),
                'heading' => __('Loading Error')
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
}
