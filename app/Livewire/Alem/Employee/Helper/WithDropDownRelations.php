<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Models\Alem\Department;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
use App\Models\Spatie\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;

/**
 * Universeller Trait für die Verwaltung von Dropdown-Collections.
 * Funktioniert mit und ohne Modal.
 */
trait WithDropDownRelations
{
    /**
     * Arrays statt Collections für bessere Performance
     */
    public array $teams = [];
    public array $departments = [];
    public array $roles = [];
    public array $professions = [];
    public array $stages = [];
    public array $supervisors = [];

    /**
     * Kann von der verwendenden Klasse überschrieben werden
     * Standard: Keine Modal-Prüfung
     */
    protected function shouldCheckModalState(): bool
    {
        return false;
    }

    /**
     * Kann von der verwendenden Klasse überschrieben werden
     * Nur relevant wenn shouldCheckModalState() true zurückgibt
     */
    protected function isModalOpen(): bool
    {
        return true;
    }

    /**
     * Refresh eine Collection mit Standard-Logik
     */
    protected function refreshCollectionData(string $type, ?int $id = null, array $conditions = []): void
    {
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
     * Lädt alle benötigten Daten als Arrays
     */
    private function loadRelationsData(): void
    {
        if (!$this->companyId) {
            return;
        }

        try {
            $this->teams = Team::getCompanyTeams($this->companyId)
                ->map(fn($team) => [
                    'id' => $team->id,
                    'name' => $team->name
                ])
                ->toArray();

            $this->departments = Department::getDepartmentsForTeam($this->currentTeamId)
                ->map(fn($dept) => [
                    'id' => $dept->id,
                    'name' => $dept->name
                ])
                ->toArray();

            $this->roles = Role::getEmployeePanelRoles($this->companyId)
                ->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'is_manager' => $role->is_manager ?? false
                ])
                ->toArray();

            $this->professions = Profession::getCompanyProfessions($this->companyId)
                ->map(fn($prof) => [
                    'id' => $prof->id,
                    'name' => $prof->name
                ])
                ->toArray();

            $this->stages = Stage::getCompanyStages($this->companyId)
                ->map(fn($stage) => [
                    'id' => $stage->id,
                    'name' => $stage->name
                ])
                ->toArray();

            $this->supervisors = $this->loadSupervisors();

        } catch (\Exception $e) {
            // Collections bleiben leer bei Fehler
        } catch (\Throwable $e) {
            $this->handleLoadingError($e);
        }
    }

    #[On(['profession-created', 'profession-updated', 'profession-deleted'])]
    public function refreshProfessions(?int $id = null): void
    {
        $this->refreshCollectionData('professions', $id, [
            'companyId' => true
        ]);
    }

    #[On(['stage-created', 'stage-updated', 'stage-deleted'])]
    public function refreshStages(?int $id = null): void
    {
        $this->refreshCollectionData('stages', $id, [
            'companyId' => true
        ]);
    }

    #[On(['department-updated', 'department-created', 'department-deleted'])]
    public function refreshDepartments(?int $id = null): void
    {
        $this->refreshCollectionData('departments', $id, [
            'currentTeamId' => true
        ]);
    }

    /**
     * Lädt Supervisors als Array
     */
    private function loadSupervisors(): array
    {
        $supervisors = User::getCompanyManagers($this->companyId);

        return $supervisors
            ->reject(fn($sup) => $sup->id === $this->authUserId)
            ->map(fn($sup) => [
                'id' => $sup->id,
                'name' => $sup->name,
                'last_name' => $sup->last_name,
                'full_name' => $sup->name . ' ' . $sup->last_name,
                'profile_photo_path' => $sup->profile_photo_path
            ])
            ->toArray();
    }

    protected function resetDropdownCollections(): void
    {
        $this->reset([
            'teams', 'departments', 'roles',
            'professions', 'stages', 'supervisors'
        ]);
    }

    /**
     * Prüfe ob Refresh durchgeführt werden soll
     */
    protected function checkRefreshConditions(array $conditions): bool
    {
        // Modal-Check nur wenn explizit gewünscht
        if ($this->shouldCheckModalState() && !$this->isModalOpen()) {
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

    /**
     * Lädt alle Collections auf einmal - nützlich für Komponenten ohne Modal
     */
    protected function loadAllDropdownCollections(): void
    {
        foreach (array_keys($this->getCollectionConfig()) as $type) {
            $this->refreshCollectionData($type);
        }
    }
}
