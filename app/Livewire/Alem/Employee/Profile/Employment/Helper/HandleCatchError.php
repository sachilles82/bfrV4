<?php

namespace App\Livewire\Alem\Employee\Profile\Employment\Helper;

use App\Traits\Error\BaseHandleCatchError;

/**
 * Spezifischer Error Handler für Employment-bezogene Components
 */
trait HandleCatchError
{
    use BaseHandleCatchError;

    /**
     * Behandelt Fehler beim Speichern/Editieren
     */
    private function handleEditingError(\Throwable $e): void
    {
        $this->handleError($e, 'Anstellungsdaten');
    }

    /**
     * Alias für Konsistenz
     */
    private function handleSavingError(\Throwable $e): void
    {
        $this->handleEditingError($e);
    }

    /**
     * Implementierung für Employment Components
     */
    protected function collectIdentificationData(): array
    {
        return [
            'bearbeiteter_user_id' => $this->userId ?? null,
        ];
    }

    /**
     * Implementierung für Employment Components
     */
    protected function collectFormData(): array
    {
        $knownEmploymentFields = [
            'joined_at', 'personal_number', 'prob_period',
            'probation_at', 'notice_at', 'notice_period', 'leave_at'
        ];

        $formData = [];

        foreach ($knownEmploymentFields as $field) {
            if (property_exists($this, $field)) {
                $formData[$field] = $this->$field ?? null;
            }
        }

        return $formData;
    }
}
