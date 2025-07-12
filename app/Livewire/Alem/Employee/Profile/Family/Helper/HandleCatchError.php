<?php

namespace App\Livewire\Alem\Employee\Profile\Family\Helper;

use App\Traits\Error\BaseHandleCatchError;

/**
 * Spezifischer Error Handler für Child-bezogene Components
 */
trait HandleCatchError
{
    use BaseHandleCatchError;

    /**
     * Behandelt Fehler beim Speichern/Editieren von Kindern
     */
    private function handleEditingError(\Throwable $e): void
    {
        $this->handleError($e, 'Kinderdaten');
    }

    /**
     * Alias für Konsistenz
     */
    private function handleSavingError(\Throwable $e): void
    {
        $this->handleEditingError($e);
    }

    /**
     * Implementierung für Child Components
     */
    protected function collectIdentificationData(): array
    {
        $data = [
            'child_id' => null,
            'parent_user_id' => null,
        ];

        // Versuche Child ID zu finden
        if (property_exists($this, 'child') && $this->child) {
            $data['child_id'] = $this->child->id ?? null;
            $data['parent_user_id'] = $this->child->user_id ?? null;
        } elseif (property_exists($this, 'childId')) {
            $data['child_id'] = $this->childId;
        }

        // Falls user_id noch nicht gefunden, suche weiter
        if (!$data['parent_user_id'] && property_exists($this, 'userId')) {
            $data['parent_user_id'] = $this->userId;
        }

        return $data;
    }

    /**
     * Implementierung für Child Components
     */
    protected function collectFormData(): array
    {
        $knownChildFields = [
            'name', 'gender', 'birthdate', 'ahv_number', 'valid_until'
        ];

        $formData = [];

        foreach ($knownChildFields as $field) {
            if (property_exists($this, $field)) {
                $formData[$field] = $this->$field ?? null;
            }
        }

        return $formData;
    }
}
