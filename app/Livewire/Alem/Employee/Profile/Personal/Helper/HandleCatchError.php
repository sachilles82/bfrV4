<?php

namespace App\Livewire\Alem\Employee\Profile\Member\Helper;

use App\Traits\Error\BaseHandleCatchError;

/**
 * Spezifischer Error Handler für Member-bezogene Components
 */
trait HandleCatchError
{
    use BaseHandleCatchError;

    /**
     * Behandelt Fehler beim Speichern/Editieren
     */
    private function handleEditingError(\Throwable $e): void
    {
        $this->handleError($e, 'Mitglieder-Informationen');
    }

    /**
     * Behandelt Fehler beim Erstellen
     */
    private function handleSavingError(\Throwable $e): void
    {
        $this->handleError($e, 'Mitglieder-Informationen', [
            'operation_type' => 'creating'
        ]);
    }

    /**
     * Behandelt Fehler beim Laden der Relationsdaten
     */
    private function handleLoadingError(\Throwable $e): void
    {
        $this->handleError($e, 'Relationsdaten', [
            'operation_type' => 'loading'
        ]);
    }

    /**
     * Implementierung für Member Components
     */
    protected function collectIdentificationData(): array
    {
        return [
            'bearbeiteter_user_id' => $this->userId ?? null,
        ];
    }

    /**
     * Implementierung für Member Components
     */
    protected function collectFormData(): array
    {
        $formData = [];

        // Skalare Felder
        $scalarFields = ['status', 'department', 'profession', 'stage', 'supervisor'];
        foreach ($scalarFields as $field) {
            if (property_exists($this, $field)) {
                $formData[$field] = $this->$field ?? null;
            }
        }

        // Array Felder
        if (property_exists($this, 'selectedTeams')) {
            $formData['selectedTeams'] = $this->selectedTeams ?? [];
        }
        if (property_exists($this, 'selectedRoles')) {
            $formData['selectedRoles'] = $this->selectedRoles ?? [];
        }

        return $formData;
    }
}
