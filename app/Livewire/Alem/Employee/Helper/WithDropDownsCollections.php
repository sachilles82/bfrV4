<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Models\Alem\Department;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
use App\Models\Spatie\Role;
use Illuminate\Support\Collection;

/**
 * Trait für die Fehlerbehandlung in der CreateEmployee-Komponente.
 */
trait WithDropDownsCollections
{
    /**
     * Refresh eine Collection mit Standard-Logik
     */
    protected function refreshCollectionData(string $type, ?int $id = null, array $conditions = [] ): void {
        // Mapping von Type zu Properties
        $config = $this->getCollectionConfig()[$type] ?? null;

        if (!$config) {
            return;
        }

        // Prüfe Bedingungen
        if (!$this->checkRefreshConditions($conditions)) {
            return;
        }

        // Lade Daten neu
        $data = call_user_func($config['loader']);

        // Konvertiere zu Array wenn nötig
        if ($data instanceof Collection) {
            $data = $data->map(fn($item) =>
            $config['mapper'] ? call_user_func($config['mapper'], $item) : $item->toArray()
            )->toArray();
        }

        // Setze Collection
        $this->{$config['collection']} = $data;

        // Handle Selection
        $this->handleSelection($config, $data, $id);
    }

    /**
     * Konfiguration für alle Collections
     */
    protected function getCollectionConfig(): array
    {
        return [
            'professions' => [
                'label' => 'Professions',
                'collection' => 'professions',
                'selected' => 'profession',
                'loader' => fn() => Profession::getCompanyProfessions($this->companyId),
                'mapper' => fn($item) => ['id' => $item->id, 'name' => $item->name],
            ],
            'stages' => [
                'label' => 'Stages',
                'collection' => 'stages',
                'selected' => 'stage',
                'loader' => fn() => Stage::getCompanyStages($this->companyId),
                'mapper' => fn($item) => ['id' => $item->id, 'name' => $item->name],
            ],
            'departments' => [
                'label' => 'Departments',
                'collection' => 'departments',
                'selected' => 'department',
                'loader' => fn() => Department::getDepartmentsForTeam($this->currentTeamId),
                'mapper' => fn($item) => ['id' => $item->id, 'name' => $item->name],
            ],
            'roles' => [
                'label' => 'Roles',
                'collection' => 'roles',
                'selected' => 'selectedRoles',
                'loader' => fn() => Role::getEmployeePanelRoles($this->companyId),
                'mapper' => fn($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'is_manager' => $item->is_manager ?? false
                ],
                'multiple' => true,
            ],
        ];
    }

    /**
     * Prüfe ob Refresh durchgeführt werden soll
     */
    protected function checkRefreshConditions(array $conditions): bool
    {
        // Standard-Check: Modal muss offen sein
        if (!$this->showCreateModal) {
            return false;
        }

        // Weitere Bedingungen prüfen
        foreach ($conditions as $property => $required) {
            if ($required && !$this->$property) {
                return false;
            }
        }

        return true;
    }

    /**
     * Handle die Auswahl nach dem Refresh
     */
    protected function handleSelection(array $config, array $data, ?int $id): void
    {
        $selectedProperty = $config['selected'];
        $ids = array_column($data, 'id');

        // Multiple Selection (z.B. Roles)
        if ($config['multiple'] ?? false) {
            if ($id && !in_array($id, $this->$selectedProperty)) {
                $this->$selectedProperty[] = $id;
            }

            // Filter ungültige IDs
            $this->$selectedProperty = array_filter(
                $this->$selectedProperty,
                fn($selectedId) => in_array($selectedId, $ids)
            );
        }
        // Single Selection
        else {
            if ($id) {
                $this->$selectedProperty = $id;
            }

            // Prüfe ob ausgewählter Wert noch existiert
            if ($this->$selectedProperty && !in_array($this->$selectedProperty, $ids)) {
                $this->$selectedProperty = null;
            }
        }
    }
}
