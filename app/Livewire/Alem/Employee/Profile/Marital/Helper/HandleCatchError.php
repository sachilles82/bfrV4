<?php

namespace App\Livewire\Alem\Employee\Profile\Marital\Helper;

use App\Traits\Error\BaseHandleCatchError;

/**
 * Spezifischer Error Handler für Marital-bezogene Components
 */
trait HandleCatchError
{
    use BaseHandleCatchError;

    /**
     * Behandelt Fehler beim Speichern/Editieren
     */
    private function handleEditingError(\Throwable $e): void
    {
        $this->handleError($e, 'Ehestatus-Daten');
    }

    /**
     * Alias für Konsistenz
     */
    private function handleSavingError(\Throwable $e): void
    {
        $this->handleEditingError($e);
    }

    /**
     * Implementierung für Marital Components
     */
    protected function collectIdentificationData(): array
    {
        return [
            'bearbeiteter_user_id' => $this->userId ?? null,
        ];
    }

    /**
     * Implementierung für Marital Components
     */
    protected function collectFormData(): array
    {
        $knownMaritalFields = [
            'civil_status', 'name_partner', 'single_parent',
            'birthdate_partner', 'ahv_partner', 'marriage_at'
        ];

        $formData = [];

        foreach ($knownMaritalFields as $field) {
            if (property_exists($this, $field)) {
                $formData[$field] = $this->$field ?? null;
            }
        }

        return $formData;
    }
}
