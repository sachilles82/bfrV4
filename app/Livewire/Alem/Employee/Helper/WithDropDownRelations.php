<?php

namespace App\Livewire\Alem\Employee\Helper;

use App\Models\Alem\Department;
use App\Models\Alem\QuickCrud\Profession;
use App\Models\Alem\QuickCrud\Stage;
use App\Models\Spatie\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

trait WithDropDownRelations
{
    /**
     * Arrays für Dropdown-Daten
     */
    public array $teams = [];
    public array $departments = [];
    public array $roles = [];
    public array $professions = [];
    public array $stages = [];
    public array $supervisors = [];

    /**
     * Track welche Collections bereits geladen wurden
     */
    protected array $loadedCollections = [];

    /**
     * Kann von der verwendenden Klasse überschrieben werden
     */
    protected function shouldCheckModalState(): bool
    {
        return false;
    }

    protected function isModalOpen(): bool
    {
        return true;
    }

    /**
     * Zentrale Konfiguration für alle Collections
     */
    protected function getCollectionConfig(): array
    {
        return [
            'teams' => [
                'collection' => 'teams',
                'loader' => fn() => Team::getCompanyTeams($this->companyId),
                'mapper' => fn($item) => ['id' => $item->id, 'name' => $item->name],
                'dependencies' => ['companyId'],
            ],
            'departments' => [
                'collection' => 'departments',
                'selected' => 'department',
                'loader' => fn() => Department::getTeamDepartments($this->currentTeamId),
                'mapper' => fn($item) => ['id' => $item->id, 'name' => $item->name],
                'dependencies' => ['currentTeamId'],
            ],
            'roles' => [
                'collection' => 'roles',
                'selected' => 'selectedRoles',
                'loader' => fn() => Role::getEmployeePanelRoles($this->companyId),
                'mapper' => fn($item) => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'is_manager' => $item->is_manager ?? false
                ],
                'dependencies' => ['companyId'],
                'multiple' => true,
            ],
            'professions' => [
                'collection' => 'professions',
                'selected' => 'profession',
                'loader' => fn() => Profession::getCompanyProfessions($this->companyId),
                'mapper' => fn($item) => ['id' => $item->id, 'name' => $item->name],
                'dependencies' => ['companyId'],
            ],
            'stages' => [
                'collection' => 'stages',
                'selected' => 'stage',
                'loader' => fn() => Stage::getCompanyStages($this->companyId),
                'mapper' => fn($item) => ['id' => $item->id, 'name' => $item->name],
                'dependencies' => ['companyId'],
            ],
            'supervisors' => [
                'collection' => 'supervisors',
                'selected' => 'supervisor',
                'loader' => fn() => $this->loadSupervisors(),
                'mapper' => null, // Bereits in loadSupervisors gemappt
                'dependencies' => ['companyId', 'authUserId'],
            ],
        ];
    }

    /**
     * NEU: Lädt nur spezifische Collections
     *
     * @param array $collections Wenn leer, werden ALLE geladen (backward compatibility)
     */
    protected function loadRelationsData(array $collections = []): void
    {
        if (!$this->companyId) {
            return;
        }

        try {
            $config = $this->getCollectionConfig();

            // Wenn keine spezifischen Collections angegeben, lade alle
            $collectionsToLoad = empty($collections)
                ? $config
                : array_intersect_key($config, array_flip($collections));

            foreach ($collectionsToLoad as $configItem) {
                $data = call_user_func($configItem['loader']);

                if ($data instanceof Collection && isset($configItem['mapper'])) {
                    $this->{$configItem['collection']} = $data->map($configItem['mapper'])->toArray();
                } else {
                    $this->{$configItem['collection']} = $data instanceof Collection ? $data->toArray() : $data;
                }
            }
        } catch (\Throwable $e) {
            $this->handleLoadingError($e);
            $this->resetDropdownRelationsData();
        }
    }

    /**
     * NEU: Lädt eine einzelne Collection mit Dependency-Check und Caching
     */
    protected function loadSingleCollection(string $collectionName): bool
    {
        // Skip wenn bereits geladen
        if (isset($this->loadedCollections[$collectionName])) {
            return true;
        }

        $config = $this->getCollectionConfig()[$collectionName] ?? null;
        if (!$config) {
            return false;
        }

        // Prüfe Dependencies
        foreach ($config['dependencies'] ?? [] as $dependency) {
            if (!isset($this->$dependency) || !$this->$dependency) {
                return false;
            }
        }

        try {
            $data = call_user_func($config['loader']);

            if ($data instanceof Collection && isset($config['mapper'])) {
                $this->{$config['collection']} = $data->map($config['mapper'])->toArray();
            } else {
                $this->{$config['collection']} = $data instanceof Collection ? $data->toArray() : $data;
            }

            $this->loadedCollections[$collectionName] = true;
            return true;

        } catch (\Throwable $e) {
            $this->handleLoadingError($e);
            return false;
        }
    }

    /**
     * NEU: Lazy Loading Getter für Collections
     */
    public function __get($property)
    {
        $config = $this->getCollectionConfig();

        // Prüfe ob es eine unserer Collections ist
        foreach ($config as $name => $conf) {
            if ($conf['collection'] === $property && !isset($this->loadedCollections[$name])) {
                $this->loadSingleCollection($name);
                return $this->$property;
            }
        }

        return parent::__get($property);
    }

    /**
     * Optimierte Supervisor-Ladung
     */
    protected function loadSupervisors(): array
    {
        $excludeId = $this->employeeId;

        return User::getCompanyManagers($this->companyId)
            ->reject(fn($sup) => $sup->id === $excludeId)
            ->map(fn($sup) => [
                'id' => $sup->id,
                'full_name' => $sup->name,
                'profile_photo_path' => $sup->profile_photo_path

            ])
            ->values()
            ->toArray();
    }

    /**
     * Refresh mit Force-Reload
     */
    protected function refreshCollectionData(string $type, ?int $id = null, array $conditions = []): void
    {
        if (!$this->checkRefreshConditions($conditions)) {
            return;
        }

        // Force reload by removing from loaded collections
        unset($this->loadedCollections[$type]);

        // Clear current data
        $config = $this->getCollectionConfig()[$type] ?? null;
        if ($config && isset($config['collection'])) {
            $this->{$config['collection']} = [];
        }

        // Reload
        if ($this->loadSingleCollection($type)) {
            $this->handleSelection($config, $this->{$config['collection']}, $id);
        }
    }

    /**
     * Event Listeners
     */
    #[On(['profession-created', 'profession-updated', 'profession-deleted'])]
    public function refreshProfessions(?int $id = null): void
    {
        $this->refreshCollectionData('professions', $id, ['companyId' => true]);
    }

    #[On(['stage-created', 'stage-updated', 'stage-deleted'])]
    public function refreshStages(?int $id = null): void
    {
        $this->refreshCollectionData('stages', $id, ['companyId' => true]);
    }

    #[On(['department-updated', 'department-created', 'department-deleted'])]
    public function refreshDepartments(?int $id = null): void
    {
        $this->refreshCollectionData('departments', $id, ['currentTeamId' => true]);
    }

    /**
     * Erweiterte Reset-Funktion
     */
    protected function resetDropdownRelationsData(): void
    {
        $collections = array_column($this->getCollectionConfig(), 'collection');
        $this->reset($collections);
        $this->loadedCollections = [];
    }

    /**
     * Prüfe ob Refresh durchgeführt werden soll
     */
    protected function checkRefreshConditions(array $conditions): bool
    {
        if ($this->shouldCheckModalState() && !$this->isModalOpen()) {
            return false;
        }

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
        if (!isset($config['selected'])) return;

        $selectedProperty = $config['selected'];
        $ids = array_column($data, 'id');

        if ($config['multiple'] ?? false) {
            if ($id && !in_array($id, $this->$selectedProperty)) {
                $this->$selectedProperty[] = $id;
            }
            $this->$selectedProperty = array_values(
                array_filter($this->$selectedProperty, fn($selectedId) => in_array($selectedId, $ids))
            );
        } else {
            if ($id) {
                $this->$selectedProperty = $id;
            }
            if ($this->$selectedProperty && !in_array($this->$selectedProperty, $ids)) {
                $this->$selectedProperty = null;
            }
        }
    }

    /**
     * NEU: Helper-Methoden für bessere Kontrolle
     */
    public function isCollectionLoaded(string $collection): bool
    {
        return isset($this->loadedCollections[$collection]);
    }

    public function getLoadedCollections(): array
    {
        return array_keys($this->loadedCollections);
    }

    public function forceReloadCollection(string $collection): void
    {
        unset($this->loadedCollections[$collection]);
        $this->loadSingleCollection($collection);
    }

    /**
     * Debug: Prüfe ob Daten aus Cache kommen
     */
    public function debugCacheStatus(): array
    {
        $status = [];

        // Prüfe Roles Cache
        $rolesCacheKey = "roles:company:{$this->companyId}";
        $status['roles'] = [
            'cache_exists' => Cache::has($rolesCacheKey),
            'cache_key' => $rolesCacheKey,
            'loaded_count' => count($this->roles),
            'from_cache' => $this->isCollectionLoaded('roles')
        ];

        // Prüfe Supervisors Cache
        $supervisorsCacheKey = "users:company:{$this->companyId}:managers";
        $status['supervisors'] = [
            'cache_exists' => Cache::has($supervisorsCacheKey),
            'cache_key' => $supervisorsCacheKey,
            'loaded_count' => count($this->supervisors),
            'from_cache' => $this->isCollectionLoaded('supervisors')
        ];

        return $status;
    }
}
