<?php

namespace App\Livewire\Alem\Employee\Profile\Family\Helper;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait HandleCatchError
{
    /**
     * Behandelt Fehler beim Speichern/Editieren von Kindern
     */
    private function handleSavingError(\Throwable $e): void
    {
        // Rollback der Transaktion
        if (DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        // Logge den Fehler
        Log::error('Fehler beim Speichern der Kinderdaten', [
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine(),
            'parent_user_id' => $this->userId ?? null,
            'child_id' => $this->childId ?? null,
            'auth_user_id' => $this->authUserId ?? auth()->id(),
            'form_data' => [
                'name' => $this->name ?? null,
                'gender' => $this->gender ?? null,
                'birthdate' => $this->birthdate ?? null,
                'ahv_number' => $this->ahv_number ?? null,
                'valid_until' => $this->valid_until ?? null,
            ]
        ]);

        // Benutzerfreundliche Fehlermeldung
        Flux::toast(
            text: __('Error saving child data. Please try again.'),
            heading: __('Save Error'),
            variant: 'danger'
        );

        // Debug-Info in Development
        if (config('app.debug')) {
            Flux::toast(
                text: 'Debug: ' . $e->getMessage(),
                heading: 'Error Details',
                variant: 'warning'
            );
        }
    }

    /**
     * Behandelt Fehler beim Editieren
     */
    private function handleEditingError(\Throwable $e): void
    {
        $this->handleSavingError($e);
    }
}
