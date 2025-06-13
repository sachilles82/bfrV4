<?php

namespace App\Traits\Livewire;

use App\Models\User;
use App\Models\Spatie\Role;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

/**
 * Trait WithSupervisorManagement
 *
 * Erweitert Livewire Components um Supervisor/Manager-Funktionalität
 * Arbeitet zusammen mit WithDropDownRelations
 */
trait WithSupervisorManagement
{
    // Properties für Supervisor-Management
    public ?int $supervisor = null;

    // Cache für Supervisors (wird nicht serialisiert)
    private ?Collection $cachedSupervisors = null;

    /**
     * Initialisiert Supervisor-Management
     * Sollte in mount() oder beim Modal-Öffnen aufgerufen werden
     */
    protected function initializeSupervisorManagement(): void
    {
        $this->supervisor = null;
        $this->cachedSupervisors = null;
    }

    /**
     * Computed Property für verfügbare Supervisors
     * Nutzt Cache und filtert basierend auf Context
     */
    #[Computed]
    public function supervisors(): Collection
    {
        if (!$this->shouldLoadSupervisors()) {
            return collect();
        }

        if ($this->cachedSupervisors === null) {
            $companyId = $this->getCompanyIdForSupervisors();
            $this->cachedSupervisors = User::getCompanyManagers($companyId);
        }

        return $this->filterSupervisors($this->cachedSupervisors);
    }

    /**
     * Bestimmt ob Supervisors geladen werden sollen
     * Kann in Child-Klassen überschrieben werden
     */
    protected function shouldLoadSupervisors(): bool
    {
        // Prüfe ob Modal offen ist (wenn Property existiert)
        if (property_exists($this, 'showEditModal') && !$this->showEditModal) {
            return false;
        }

        if (property_exists($this, 'showCreateModal') && !$this->showCreateModal) {
            return false;
        }

        // Prüfe ob Company ID verfügbar ist
        return $this->getCompanyIdForSupervisors() !== null;
    }

    /**
     * Holt die Company ID für Supervisor-Abfragen
     * Nutzt verschiedene Quellen je nach Component
     */
    protected function getCompanyIdForSupervisors(): ?int
    {
        // Priorität 1: Direkte companyId Property
        if (property_exists($this, 'companyId') && $this->companyId) {
            return $this->companyId;
        }

        // Priorität 2: Von User Model
        if (property_exists($this, 'user') && $this->user && $this->user->company_id) {
            return $this->user->company_id;
        }

        // Priorität 3: Von Auth User
        if (auth()->check() && auth()->user()->company_id) {
            return auth()->user()->company_id;
        }

        return null;
    }

    /**
     * Filtert die Supervisor-Liste
     * Standard: Schließt aktuellen User aus
     */
    protected function filterSupervisors(Collection $supervisors): Collection
    {
        // Bei Edit: Aktuellen User ausschließen
        if (property_exists($this, 'userId') && $this->userId) {
            return $supervisors->reject(function ($supervisor) {
                return $supervisor && isset($supervisor->id) && $supervisor->id === $this->userId;
            });
        }

        return $supervisors;
    }

    /**
     * Synchronisiert Rollen und verwaltet Manager-Cache
     * Erweitert die bestehende syncRelations Funktionalität
     */
    protected function syncRolesWithManagerCache(): void
    {
        if (!property_exists($this, 'user') || !$this->user) {
            Log::warning('syncRolesWithManagerCache called without user');
            return;
        }

        if (!property_exists($this, 'selectedRoles')) {
            Log::warning('syncRolesWithManagerCache called without selectedRoles property');
            return;
        }

        // Status vor Änderung
        $wasManager = $this->user->hasManagerRole();

        // Synchronisiere Rollen
        $this->user->roles()->sync($this->selectedRoles);

        // Lade Rollen neu
        $this->user->load('roles');

        // Status nach Änderung
        $isManagerNow = $this->user->hasManagerRole();

        // Cache-Management bei Status-Änderung
        if ($wasManager !== $isManagerNow) {
            User::clearManagerCache($this->user->company_id);
            $this->forceReloadSupervisors();

            // Dispatch Event
            if ($isManagerNow) {
                $this->dispatch('manager-added', userId: $this->user->id);
            } else {
                $this->dispatch('manager-removed', userId: $this->user->id);
            }

            Log::info('Manager status changed', [
                'user_id' => $this->user->id,
                'was_manager' => $wasManager,
                'is_manager_now' => $isManagerNow
            ]);
        }
    }

    /**
     * Prüft ob neue Rollen Manager-Rollen enthalten
     */
    protected function hasManagerRoleInSelectedRoles(): bool
    {
        if (!property_exists($this, 'selectedRoles') || empty($this->selectedRoles)) {
            return false;
        }

        return User::hasManagerRoleInList($this->selectedRoles);
    }

    /**
     * Force Reload der Supervisor-Liste
     * Nützlich nach Änderungen
     */
    public function forceReloadSupervisors(): void
    {
        $this->cachedSupervisors = null;

        // Computed Property Reset
        if (method_exists($this, 'forceReloadCollection')) {
            $this->forceReloadCollection('supervisors');
        } else {
            unset($this->supervisors);
        }

        Log::debug('Supervisors force reloaded');
    }

    /**
     * Reset Supervisor-Daten
     * Sollte beim Schließen von Modals aufgerufen werden
     */
    protected function resetSupervisorData(): void
    {
        $this->supervisor = null;
        $this->cachedSupervisors = null;

        Log::debug('Supervisor data reset');
    }

    /**
     * Validiert Supervisor-Auswahl
     */
    protected function validateSupervisorSelection(): bool
    {
        if (!$this->supervisor) {
            return true; // Kein Supervisor ist valide
        }

        // Prüfe ob Supervisor existiert und Manager ist
        $supervisorUser = User::find($this->supervisor);
        if (!$supervisorUser) {
            $this->addError('supervisor', 'Der ausgewählte Supervisor existiert nicht.');
            return false;
        }

        if (!$supervisorUser->hasManagerRole()) {
            $this->addError('supervisor', 'Der ausgewählte Benutzer ist kein Manager.');
            return false;
        }

        // Bei Edit: Prüfe Hierarchie
        if (property_exists($this, 'userId') && $this->userId) {
            if ($this->userId === $this->supervisor) {
                $this->addError('supervisor', 'Ein Mitarbeiter kann nicht sein eigener Supervisor sein.');
                return false;
            }

            // Weitere Hierarchie-Validierung wenn nötig
            try {
                $valid = \App\Models\Employee::validateSupervisorHierarchy($this->userId, $this->supervisor);
                if (!$valid) {
                    $this->addError('supervisor', 'Diese Zuweisung würde eine zirkuläre Hierarchie erstellen.');
                    return false;
                }
            } catch (\Exception $e) {
                $this->addError('supervisor', $e->getMessage());
                return false;
            }
        }

        return true;
    }

    /**
     * Listener für Manager-Änderungen
     * Kann von anderen Components getriggert werden
     */
    #[On('manager-added')]
    #[On('manager-removed')]
    public function handleManagerChange($userId): void
    {
        // Reload Supervisors wenn sich Manager-Status ändert
        $this->forceReloadSupervisors();
    }

    /**
     * Helper: Prüft ob Component Supervisor-Features nutzt
     */
    protected function usesSupervisorFeatures(): bool
    {
        return property_exists($this, 'supervisor');
    }
}
