<?php

namespace App\Livewire\Alem\Employee\Profile\Account\Helper;

use App\Traits\Error\BaseHandleCatchError;

/**
 * Spezifischer Error Handler für Account-bezogene Components
 */
trait HandleCatchError
{
    use BaseHandleCatchError;

    /**
     * Behandelt Fehler beim Speichern/Editieren der Account Details
     */
    private function handleEditingError(\Throwable $e): void
    {
        $this->handleError($e, 'Account Details');
    }

    /**
     * Behandelt Fehler beim Laden der Account Details
     */
    private function handleLoadingError(\Throwable $e): void
    {
        $this->handleError($e, 'Account Details', [
            'operation_type' => 'loading'
        ]);
    }

    /**
     * Implementierung für Account Components
     */
    protected function collectIdentificationData(): array
    {
        return [
            'bearbeiteter_user_id' => $this->userId ?? null,
        ];
    }

    /**
     * Implementierung für Account Components
     */
    protected function collectFormData(): array
    {
        $knownAccountFields = [
            'gender', 'name', 'email', 'phone_1', 'phone_2', 'model_status'
        ];

        $formData = [];

        foreach ($knownAccountFields as $field) {
            if (property_exists($this, $field)) {
                $formData[$field] = $this->$field ?? null;
            }
        }

        // Füge Original-Daten hinzu wenn vorhanden
        if (property_exists($this, 'originalData') && !empty($this->originalData)) {
            $formData['original_daten'] = $this->originalData;
        }

        return $formData;
    }
}
