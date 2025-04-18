<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Http\Controllers\Flux\Alem\EmployeeController;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Department;
use App\Models\Alem\Employee\Employee;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;
use Laravel\Jetstream\HasTeams;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Models\Spatie\Role;
use App\Models\Team;
use App\Traits\Model\WithModelStatusOptions;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

#[Lazy(isolate: false)]
class EditEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee, WithModelStatusOptions;

    // Modal state
    public bool $showModal = false;
    private bool $dataLoaded = false;

    // User identification
    public ?int $userId = null;
    public ?User $user = null;

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

    // Cached data
    private $teams = null;
    private $departments = null;
    private $roles = null;
    private $professions = null;
    private $stages = null;
    private $supervisors = null;

    /**
     * Listen for edit-employee event
     */
    #[On('edit-employee')]
    public function editEmployee($userId): void
    {
        try {
            // Prüfe verschiedene mögliche Formate der User ID
            if (is_array($userId) && isset($userId['userId'])) {
                $this->userId = $userId['userId'];
            } else {
                $this->userId = $userId;
            }

            if (!$this->userId) {
                throw new \Exception('Keine gültige Benutzer-ID empfangen');
            }

            // Benutzer mit allen relevanten Relationen laden - optimiertes Eager-Loading
            $this->user = User::with([
                'employee:id,user_id,employee_status,profession_id,stage_id,supervisor_id',
                'employee.profession:id,name',
                'employee.stage:id,name',
                'teams:id,name',
                'roles:id,name',
                'department:id,name'
            ])->findOrFail($this->userId);

            // Formular mit Benutzerdaten füllen
            $this->fillFormWithUserData();

            // Modal öffnen und Daten-Lade-Flag zurücksetzen
            $this->showModal = true;
            $this->dataLoaded = false;

            // Daten sofort laden, um Timing-Probleme zu vermeiden
            $this->loadEssentialData();

            // Modal über UI anzeigen
            $this->dispatch('modal-show', ['name' => 'edit-employee']);
        } catch (\Exception $e) {
            $this->handleError('Fehler beim Laden des Mitarbeiters', $e);
        }
    }

    /**
     * Fill form with user data
     */
    protected function fillFormWithUserData(): void
    {
        if (!$this->user) {
            return;
        }

        // Reset all relevant properties
        $this->reset([
            'name', 'last_name', 'email', 'gender', 'model_status',
            'joined_at', 'department', 'selectedTeams', 'selectedRoles',
            'employee_status', 'profession', 'stage', 'supervisor'
        ]);

        // Assign user data
        $this->name = $this->user->name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->gender = $this->user->gender;
        $this->model_status = $this->user->model_status;
        $this->joined_at = $this->user->joined_at;
        $this->department = $this->user->department_id;

        // Assign relationships
        $this->selectedTeams = $this->user->teams->pluck('id')->toArray();
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();

        // Assign employee data if exists
        if ($this->user->employee) {
            // IMPORTANT: Convert enum to string value for form binding
            $this->employee_status = $this->user->employee->employee_status instanceof EmployeeStatus
                ? $this->user->employee->employee_status->value
                : $this->user->employee->employee_status;

            $this->profession = $this->user->employee->profession_id;
            $this->stage = $this->user->employee->stage_id;
            $this->supervisor = $this->user->employee->supervisor_id;
        }
    }

    /**
     * Load all data needed for form dropdowns
     */
    protected function loadEssentialData(): void
    {
        if ($this->dataLoaded) {
            return;
        }

        try {
            // Store user context values to avoid multiple auth() calls
            $currentUser = auth()->user();
            $currentUserId = $currentUser->id;
            $companyId = $currentUser->company_id;
            $teamId = !empty($this->selectedTeams) ? $this->selectedTeams[0] : $currentUser->current_team_id;

            // Use a transaction to batch all queries together
            DB::transaction(function() use ($currentUserId, $companyId, $teamId) {
                // Load teams
                if ($this->teams === null) {
                    $this->teams = Team::where('company_id', $companyId)
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();
                }

                // Load departments filtered by team
                if ($this->departments === null) {
                    $this->departments = Department::where('model_status', ModelStatus::ACTIVE->value)
                        ->where('company_id', $companyId)
                        ->when($teamId, function ($query) use ($teamId) {
                            return $query->where('team_id', $teamId);
                        })
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();
                }

                // Load roles
                if ($this->roles === null) {
                    $this->roles = Role::where('access', RoleHasAccessTo::EmployeePanel->value)
                        ->where('visible', RoleVisibility::Visible->value)
                        ->where(function ($query) use ($currentUserId) {
                            $query->where('created_by', 1)
                                ->orWhere('created_by', $currentUserId);
                        })
                        ->select(['id', 'name', 'is_manager'])
                        ->get();
                }

                // Load professions
                if ($this->professions === null) {
                    $this->professions = Profession::where('company_id', $companyId)
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();
                }

                // Load stages
                if ($this->stages === null) {
                    $this->stages = Stage::where('company_id', $companyId)
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();
                }

                // Load supervisors, excluding current user
                if ($this->supervisors === null) {
                    $this->supervisors = User::select(['users.id', 'users.name', 'users.last_name', 'users.profile_photo_path'])
                        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                        ->join('roles', function($join) {
                            $join->on('model_has_roles.role_id', '=', 'roles.id')
                                ->where('roles.is_manager', true)
                                ->where('model_has_roles.model_type', User::class);
                        })
                        ->where('users.company_id', $companyId)
                        ->whereNull('users.deleted_at')
                        ->when($this->userId, function ($query) {
                            return $query->where('users.id', '!=', $this->userId);
                        })
                        ->distinct()
                        ->get();
                }
            }, 3); // Using lower isolation level (3) for better read performance

            $this->dataLoaded = true;
        } catch (\Exception $e) {
            $this->handleError('Error loading form data', $e);
        }
    }

    /**
     * Update employee in database
     */
    public function updateEmployee(): void
    {
        if (!$this->dataLoaded) {
            $this->loadEssentialData();
        }

        // Stelle sicher, dass employee_status ein String-Wert ist, nicht ein Enum-Objekt
        if ($this->employee_status instanceof EmployeeStatus) {
            $this->employee_status = $this->employee_status->value;
        }

        // Stelle sicher, dass model_status ein String-Wert ist, nicht ein Enum-Objekt
        if ($this->model_status instanceof ModelStatus) {
            $this->model_status = $this->model_status->value;
        }

        try {
            // Validiere die Eingaben
            $validatedData = $this->validate();

            if (!$this->userId || !$this->user) {
                throw new \Exception('Kein Mitarbeiter zur Aktualisierung ausgewählt.');
            }

            DB::beginTransaction();

            // Update user data
            try {
                $userData = [
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'gender' => $this->gender,
                    'model_status' => $this->model_status,
                    'joined_at' => Carbon::parse($this->joined_at)->format('Y-m-d'),
                    'department_id' => $this->department,
                ];

                User::where('id', $this->userId)->update($userData);

            } catch (\Exception $e) {
                DB::rollBack();
                $this->handleError('Fehler beim Aktualisieren der Benutzerdaten', $e);
                return;
            }

            // Update or create employee data
            $employeeData = [
                'employee_status' => $this->employee_status,
                'profession_id' => $this->profession,
                'stage_id' => $this->stage,
                'supervisor_id' => $this->supervisor,
            ];

            try {
                // Effiziente Methode zur Aktualisierung oder Erstellung von Mitarbeiterdaten
                Employee::updateOrCreate(
                    ['user_id' => $this->userId],
                    $employeeData
                );

            } catch (\Exception $e) {
                DB::rollBack();
                $this->handleError('Fehler beim Aktualisieren der Mitarbeiterdaten', $e);
                return;
            }

            // Update department
            try {
                $user = User::findOrFail($this->userId);
                // User hat nur ein department, keine departments-Relation
                $user->update(['department_id' => $this->department]);
            } catch (\Exception $e) {
                DB::rollBack();
                $this->handleError('Fehler beim Aktualisieren der Abteilung', $e);
                return;
            }

            // Update roles
            try {
                // User hat eine roles() Beziehung statt der syncRoles()-Methode
                $user->roles()->sync($this->selectedRoles);
            } catch (\Exception $e) {
                DB::rollBack();
                $this->handleError('Fehler beim Aktualisieren der Rollen', $e);
                return;
            }

            // Update teams
            try {
                $user->teams()->sync($this->selectedTeams);
            } catch (\Exception $e) {
                DB::rollBack();
                $this->handleError('Fehler beim Aktualisieren der Teams', $e);
                return;
            }

            // Commit and dispatch refresh event
            DB::commit();

            // Cache für diesen Mitarbeiter invalidieren
            $this->refreshRelatedCaches();

            // Erfolgsmeldung anzeigen
            session()->flash('success', 'Mitarbeiter wurde erfolgreich aktualisiert.');

            // Modal schließen und UI aktualisieren
            $this->reset('showModal');
            $this->dispatch('employee-updated');
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Mitarbeiter wurde erfolgreich aktualisiert.'
            ]);

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
     *
     * @param string $message
     * @param \Exception $exception
     */
    private function handleError(string $message, \Exception $exception): void
    {
        // Log the error
        \Log::error("{$message}: " . $exception->getMessage(), [
            'exception' => $exception->getMessage(),
            'user_id' => $this->userId,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        // Benutzerfreundliche Fehlermeldung anzeigen
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

        // Häufige Datenbankfehler in benutzerfreundliche Nachrichten übersetzen
        if (strpos($message, 'Duplicate entry') !== false && strpos($message, 'email') !== false) {
            return 'Diese E-Mail-Adresse wird bereits verwendet.';
        }

        if (strpos($message, 'Column not found') !== false) {
            return 'Datenbankfehler: Ein erforderliches Feld konnte nicht gefunden werden.';
        }

        // Bei Integritätsproblemen
        if (strpos($message, 'foreign key constraint fails') !== false) {
            return 'Beziehungsproblem: Ein verknüpfter Datensatz existiert nicht oder kann nicht aktualisiert werden.';
        }

        // Gib eine allgemeine Nachricht zurück, wenn keine spezifische Übersetzung gefunden wurde
        // Technische Details werden nur im Log gespeichert
        return 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.';
    }

    /**
     * Handle property updates
     */
    public function updated($propertyName): void
    {
        // Ensure data is loaded when modal is shown
        if ($propertyName === 'showModal' && $this->showModal && !$this->dataLoaded) {
            $this->loadEssentialData();
        }

        // Reset departments when team selection changes
        if ($propertyName === 'selectedTeams') {
            $this->departments = null; // Reset departments cache

            // If a team is selected and current department doesn't belong to it, reset department
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
        if ($this->showModal && !$this->dataLoaded) {
            $this->loadEssentialData();
        }
    }

    /**
     * Update cache when professions are updated
     */
    #[On('profession-updated')]
    public function refreshProfessions(): void
    {
        $this->professions = null;
    }

    /**
     * Update cache when stages are updated
     */
    #[On('stage-updated')]
    public function refreshStages(): void
    {
        $this->stages = null;
    }

    /**
     * Update cache when departments are created/updated
     */
    #[On('department-created')]
    #[On('department-updated')]
    public function refreshDepartments(): void
    {
        $this->departments = null;
    }

    /**
     * Get employee status options for dropdown - using static caching
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
        // Reset all component properties
        $this->reset([
            'userId', 'showModal', 'dataLoaded',
            'name', 'last_name', 'email', 'gender',
            'model_status', 'joined_at', 'department',
            'selectedTeams', 'selectedRoles',
            'employee_status', 'profession', 'stage', 'supervisor'
        ]);

        // Reset all cached data
        $this->user = null;
        $this->teams = null;
        $this->departments = null;
        $this->roles = null;
        $this->professions = null;
        $this->stages = null;
        $this->supervisors = null;

        // Close modal through proper Flux event
        $this->dispatch('modal-close', ['name' => 'edit-employee']);
    }

    /**
     * Get teams for dropdown
     */
    public function getTeamsProperty()
    {
        if ($this->showModal && $this->teams === null) {
            $this->loadEssentialData();
        }
        return $this->teams ?? collect();
    }

    /**
     * Get departments for dropdown
     */
    public function getDepartmentsProperty()
    {
        if ($this->showModal && $this->departments === null) {
            $this->loadEssentialData();
        }
        return $this->departments ?? collect();
    }

    /**
     * Get roles for dropdown
     */
    public function getRolesProperty()
    {
        if ($this->showModal && $this->roles === null) {
            $this->loadEssentialData();
        }
        return $this->roles ?? collect();
    }

    /**
     * Get professions for dropdown
     */
    public function getProfessionsProperty()
    {
        if ($this->showModal && $this->professions === null) {
            $this->loadEssentialData();
        }
        return $this->professions ?? collect();
    }

    /**
     * Get stages for dropdown
     */
    public function getStagesProperty()
    {
        if ($this->showModal && $this->stages === null) {
            $this->loadEssentialData();
        }
        return $this->stages ?? collect();
    }

    /**
     * Get supervisors for dropdown
     */
    public function getSupervisorsProperty()
    {
        if ($this->showModal && $this->supervisors === null) {
            $this->loadEssentialData();
        }
        return $this->supervisors ?? collect();
    }

    /**
     * Definiert die Validierungsregeln für die Mitarbeiterbearbeitung
     */
    public function rules(): array
    {
        return [
            // User-Felder
            'gender' => ['required', Rule::in(array_column(Gender::cases(), 'value'))],
            'name' => 'required|string|min:3',
            'last_name' => 'required|string|min:3',
            'email' => [
                'required', 'email', Rule::unique('users', 'email')->ignore($this->userId ?? null)
            ],

            // Rollen-Validierung
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'exists:roles,id',

            // Team-Auswahl
            'selectedTeams' => 'required|array|min:1',
            'selectedTeams.*' => 'exists:teams,id',

            // Abteilung
            'department' => 'required|exists:departments,id',

            // Vorgesetzter
            'supervisor' => ['required', 'exists:users,id', 'different:userId'],

            // Mitarbeiter-Felder
            'profession' => 'required|exists:App\Models\Alem\Employee\Setting\Profession,id',
            'stage' => 'required|exists:App\Models\Alem\Employee\Setting\Stage,id',
            'joined_at' => 'required|date|before_or_equal:today',

            // Status-Felder
            'model_status' => ['required', 'string', new Enum(ModelStatus::class)],
            'employee_status' => ['required', Rule::in(array_column(EmployeeStatus::cases(), 'value'))],
        ];
    }

    /**
     * Render component
     */
    public function render(): View
    {
        if ($this->showModal && !$this->dataLoaded) {
            $this->loadEssentialData();
        }

        return view('livewire.alem.employee.edit', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }

    /**
     * Cache für relevante Daten invalidieren
     */
    protected function refreshRelatedCaches(): void
    {
        // Spatie Permission Cache zurücksetzen
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Lokale Caches zurücksetzen
        $this->refreshProfessions();
        $this->refreshStages();
        $this->refreshDepartments();
    }
}
