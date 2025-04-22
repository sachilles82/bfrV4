<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Employee;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Models\Spatie\Role;
use App\Models\Team;
use App\Traits\Model\WithModelStatusOptions;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

#[Lazy(isolate: false)]
class EditEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee, WithModelStatusOptions;

    // Modal state
    public bool $showEditModal = false;
    private bool $dataLoadedEdit = false;

    // User identification
    public ?int $userId = null;
    public ?User $user = null;

    // Eigenschaften für vorgeladene Daten - optimiert für Livewire-Komponente
    public ?int $authUserId = null;
    public ?int $currentTeamId = null;
    public ?int $companyId = null;

    // User form fields
    public ?string $name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $gender = null;
    public ?string $model_status = null;
    public $joined_at = null;
    public $department = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    // Employee fields
    public $employee_status = null;
    public $profession = null;
    public $stage = null;
    public $supervisor = null;

    // Optimierung: Cache-Eigenschaften privat lassen
    private $teams = null;
    private $departments = null;
    private $roles = null;
    private $professions = null;
    private $stages = null;
    private $supervisors = null;

    /**
     * Mount-Methode speichert übergebene Parameter
     */
    public function mount(?int $authUserId = null, ?int $currentTeamId = null, ?int $companyId = null): void
    {
        // Speichere übergebene Parameter für spätere Verwendung
        $this->authUserId = $authUserId;
        $this->currentTeamId = $currentTeamId;
        $this->companyId = $companyId;
    }

    /**
     * Listen for edit-employee event
     */
    #[On('edit-employee')]
    public function editEmployee($userId): void
    {
        try {
            // Optimierte Prüfung des userId-Formats
            $this->userId = is_array($userId) && isset($userId['userId']) ? $userId['userId'] : $userId;

            if (!$this->userId) {
                throw new \Exception('Keine gültige Benutzer-ID empfangen');
            }

            // User mit allen relevanten Relationen laden - optimiertes Eager-Loading
            $this->user = User::with([
                'employee:id,user_id,employee_status,profession_id,stage_id,supervisor_id',
                'teams:id,name',
                'roles:id,name',
                'department:id,name'
            ])->findOrFail($this->userId);

            // Formular mit Benutzerdaten füllen
            $this->fillFormWithUserData();

            // Modal öffnen und Daten-Lade-Flag zurücksetzen
            $this->showEditModal = true;
            $this->dataLoadedEdit = false;

            // Daten sofort laden, um Timing-Probleme zu vermeiden
            $this->loadEssentialData();

            // Modal über UI anzeigen
            $this->dispatch('modal-show-edit', ['name' => 'edit-employee']);
        } catch (\Exception $e) {
            $this->handleError('Fehler beim Laden des Mitarbeiters', $e);
        }
    }

    /**
     * Fill form with user data
     */
    protected function fillFormWithUserData(): void
    {
        if (!$this->user) return;

        // Benutzergrundlage Daten
        $this->name = $this->user->name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->gender = $this->user->gender;
        $this->model_status = $this->user->model_status;
        $this->joined_at = $this->user->joined_at;
        $this->department = $this->user->department_id;

        // Beziehungen
        $this->selectedTeams = $this->user->teams->pluck('id')->toArray();
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();

        // Mitarbeiterdaten, falls vorhanden
        if ($this->user->employee) {
            // Enum-Werte als Strings für Formularbindung
            $this->employee_status = $this->user->employee->employee_status instanceof EmployeeStatus
                ? $this->user->employee->employee_status->value
                : $this->user->employee->employee_status;

            $this->profession = $this->user->employee->profession_id;
            $this->stage = $this->user->employee->stage_id;
            $this->supervisor = $this->user->employee->supervisor_id;
        }
    }

    /**
     * Load all data needed for form dropdowns - optimierte Verwendung übergebener Parameter
     */
    protected function loadEssentialData(): void
    {
        if ($this->dataLoadedEdit) return;

        try {
            // Optimierung: Verwende vorgeladene Parameter anstatt auth()->user() wiederholt aufzurufen
            $currentUserId = $this->authUserId ?? auth()->id();
            $companyId = $this->companyId ?? auth()->user()->company_id;

            // Verwende das erste ausgewählte Team oder das übergebene aktuelle Team
            $teamId = !empty($this->selectedTeams) ? $this->selectedTeams[0] : $this->currentTeamId;

            // Teams laden (falls noch nicht geladen)
            if ($this->teams === null) {
                $this->teams = Team::where('company_id', $companyId)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            // Departments laden (falls noch nicht geladen)
            if ($this->departments === null) {
                $this->departments = Department::where('model_status', ModelStatus::ACTIVE->value)
                    ->where('team_id', $teamId)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            // Rollen laden (falls noch nicht geladen)
            if ($this->roles === null) {
                $this->roles = Role::where('access', RoleHasAccessTo::EmployeePanel->value)
                    ->where('visible', RoleVisibility::Visible->value)
                    ->whereIn('created_by', [1, $currentUserId]) // Optimiert mit whereIn statt where-or
                    ->select(['id', 'name', 'is_manager'])
                    ->get();
            }

            // Professionen laden (falls noch nicht geladen)
            if ($this->professions === null) {
                $this->professions = Profession::where('company_id', $companyId)
                    ->where('team_id', $teamId)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            // Stages laden (falls noch nicht geladen)
            if ($this->stages === null) {
                $this->stages = Stage::where('company_id', $companyId)
                    ->where('team_id', $teamId)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            // Supervisors laden (falls noch nicht geladen)
            if ($this->supervisors === null) {
                // Optimierter JOIN für bessere Performance
                $this->supervisors = User::select(['users.id', 'users.name', 'users.last_name', 'users.profile_photo_path'])
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', function($join) {
                        $join->on('model_has_roles.role_id', '=', 'roles.id')
                            ->where('roles.is_manager', true);
                    })
                    ->where('model_has_roles.model_type', User::class)
                    ->where('users.company_id', $companyId)
                    ->whereNull('users.deleted_at')
                    ->when($this->userId, function ($query) {
                        return $query->where('users.id', '!=', $this->userId);
                    })
                    ->distinct()
                    ->get();
            }

            $this->dataLoadedEdit = true;
        } catch (\Exception $e) {
            $this->handleError('Error loading form data', $e);
        }
    }

    /**
     * Update employee in database
     */
    public function updateEmployee(): void
    {
        if (!$this->dataLoadedEdit) {
            $this->loadEssentialData();
        }

        // Sicherstellen, dass enums als string-werte vorliegen
        if ($this->employee_status instanceof EmployeeStatus) {
            $this->employee_status = $this->employee_status->value;
        }
        if ($this->model_status instanceof ModelStatus) {
            $this->model_status = $this->model_status->value;
        }

        try {
            // Validiere die Eingaben
            $this->validate();

            if (!$this->userId) {
                throw new \Exception('Kein Mitarbeiter zur Aktualisierung ausgewählt.');
            }

            DB::beginTransaction();

            try {
                // User-Daten in einer Abfrage aktualisieren
                User::where('id', $this->userId)->update([
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'gender' => $this->gender,
                    'model_status' => $this->model_status,
                    'joined_at' => Carbon::parse($this->joined_at)->format('Y-m-d'),
                    'department_id' => $this->department,
                ]);

                // Employee-Daten aktualisieren oder erstellen
                Employee::updateOrCreate(
                    ['user_id' => $this->userId],
                    [
                        'employee_status' => $this->employee_status,
                        'profession_id' => $this->profession,
                        'stage_id' => $this->stage,
                        'supervisor_id' => $this->supervisor,
                    ]
                );

                // Beziehungen aktualisieren
                $user = User::findOrFail($this->userId);
                $user->roles()->sync($this->selectedRoles);
                $user->teams()->sync($this->selectedTeams);

                // Transaktion abschließen
                DB::commit();

                // UI aktualisieren
                $this->showEditModal = false;
                $this->dispatch('employee-updated');
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Mitarbeiter wurde erfolgreich aktualisiert.'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (ValidationException $e) {
            // Nur bei Validierungsfehlern das Modal offen halten
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Bitte korrigieren Sie die markierten Felder.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleError('Fehler beim Aktualisieren des Mitarbeiters', $e);
        }
    }

    /**
     * Handle an error by logging it and showing a notification
     */
    private function handleError(string $message, \Exception $exception): void
    {
        Log::error("{$message}: " . $exception->getMessage(), [
            'exception' => $exception->getMessage(),
            'user_id' => $this->userId,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        $this->dispatch('notify', [
            'type' => 'error',
            'message' => $message . ': ' . $this->getUserFriendlyErrorMessage($exception)
        ]);
    }

    /**
     * Konvertiert technische Fehlermeldungen in benutzerfreundliche Nachrichten
     */
    private function getUserFriendlyErrorMessage(\Exception $exception): string
    {
        $message = $exception->getMessage();

        if (strpos($message, 'Duplicate entry') !== false && strpos($message, 'email') !== false) {
            return 'Diese E-Mail-Adresse wird bereits verwendet.';
        }
        if (strpos($message, 'Column not found') !== false) {
            return 'Datenbankfehler: Ein erforderliches Feld konnte nicht gefunden werden.';
        }
        if (strpos($message, 'foreign key constraint fails') !== false) {
            return 'Beziehungsproblem: Ein verknüpfter Datensatz existiert nicht oder kann nicht aktualisiert werden.';
        }

        return 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
    }

    /**
     * Handle property updates
     */
    public function updated($propertyName): void
    {
        // Daten laden, wenn Modal angezeigt wird
        if ($propertyName === 'showEditModal' && $this->showEditModal && !$this->dataLoadedEdit) {
            $this->loadEssentialData();
        }

        // Abteilungen zurücksetzen, wenn Team-Auswahl sich ändert
        if ($propertyName === 'selectedTeams') {
            $this->departments = null;

            // Wenn Team gewählt und aktuelle Abteilung nicht zu diesem Team gehört, Abteilung zurücksetzen
            if (!empty($this->selectedTeams) && $this->department) {
                $teamId = $this->selectedTeams[0];
                $departmentExists = Department::where('id', $this->department)
                    ->where('team_id', $teamId)
                    ->exists();

                if (!$departmentExists) {
                    $this->department = null;
                }
            }
        }
    }

    /**
     * Force hydration to load data after rendering
     */
    public function hydrate(): void
    {
        if ($this->showEditModal && !$this->dataLoadedEdit) {
            $this->loadEssentialData();
        }
    }

    // Event Handler für Cache-Invalidierung
    #[On('profession-updated')]
    public function refreshProfessions(): void
    {
        $this->professions = null;
    }

    #[On('stage-updated')]
    public function refreshStages(): void
    {
        $this->stages = null;
    }

    #[On('department-created')]
    #[On('department-updated')]
    public function refreshDepartments(): void
    {
        $this->departments = null;
    }

    /**
     * Get employee status options for dropdown - static caching
     */
    public function getEmployeeStatusOptionsProperty()
    {
        static $options = null;

        if ($options === null) {
            $options = collect(EmployeeStatus::cases())->map(function ($status) {
                return [
                    'value' => $status->value,
                    'label' => $status->label(),
                    'colors' => $status->colors(),
                    'icon' => $status->icon(),
                ];
            })->toArray();
        }

        return $options;
    }

    /**
     * Close modal and reset state
     */
    public function closeModal(): void
    {
        // Reset component properties
        $this->reset([
            'userId', 'showEditModal', 'dataLoadedEdit',
            'name', 'last_name', 'email', 'gender',
            'model_status', 'joined_at', 'department',
            'selectedTeams', 'selectedRoles',
            'employee_status', 'profession', 'stage', 'supervisor'
        ]);

        // Reset cached data
        $this->user = null;
        $this->teams = null;
        $this->departments = null;
        $this->roles = null;
        $this->professions = null;
        $this->stages = null;
        $this->supervisors = null;

        // Close modal
        $this->dispatch('modal-close-edit', ['name' => 'edit-employee']);
    }

    // Getter für Dropdown-Daten
    public function getTeamsProperty()
    {
        if ($this->showEditModal && $this->teams === null) {
            $this->loadEssentialData();
        }
        return $this->teams ?? collect();
    }

    public function getDepartmentsProperty()
    {
        if ($this->showEditModal && $this->departments === null) {
            $this->loadEssentialData();
        }
        return $this->departments ?? collect();
    }

    public function getRolesProperty()
    {
        if ($this->showEditModal && $this->roles === null) {
            $this->loadEssentialData();
        }
        return $this->roles ?? collect();
    }

    public function getProfessionsProperty()
    {
        if ($this->showEditModal && $this->professions === null) {
            $this->loadEssentialData();
        }
        return $this->professions ?? collect();
    }

    public function getStagesProperty()
    {
        if ($this->showEditModal && $this->stages === null) {
            $this->loadEssentialData();
        }
        return $this->stages ?? collect();
    }

    public function getSupervisorsProperty()
    {
        if ($this->showEditModal && $this->supervisors === null) {
            $this->loadEssentialData();
        }
        return $this->supervisors ?? collect();
    }

    /**
     * Render component
     */
    public function render(): View
    {
        if ($this->showEditModal && !$this->dataLoadedEdit) {
            $this->loadEssentialData();
        }

        return view('livewire.alem.employee.edit', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }

    /**
     * Invalidiert relevante Caches
     */
    protected function refreshRelatedCaches(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->refreshProfessions();
        $this->refreshStages();
        $this->refreshDepartments();
    }
}
