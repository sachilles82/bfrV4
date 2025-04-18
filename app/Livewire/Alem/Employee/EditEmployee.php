<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Department;
use App\Models\Alem\Employee;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use App\Models\Spatie\Role;
use App\Models\Team;
use App\Models\User;
use App\Traits\Model\WithModelStatusOptions;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class EditEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee, WithModelStatusOptions;

    /**
     * Modal state
     */
    public bool $showModal = false;
    protected bool $dataLoaded = false;

    /**
     * User identification
     */
    public ?int $userId = null;
    protected ?User $user = null;

    /**
     * User form fields
     */
    public ?string $name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $gender = null;
    public ?string $model_status = null;
    public $joined_at = null;
    public $department = null;
    public array $selectedTeams = [];
    public array $selectedRoles = [];

    /**
     * Employee fields
     */
    public $employee_status = null;
    public $profession = null;
    public $stage = null;
    public $supervisor = null;

    /**
     * Cached data collections
     */
    protected array $cache = [
        'teams' => null,
        'departments' => null,
        'roles' => null,
        'professions' => null,
        'stages' => null,
        'supervisors' => null,
    ];

    /**
     * Listener für den Empfang der Mitarbeiter-ID aus anderen Komponenten
     *
     * @param array|int $data Entweder ein Array mit 'userId' oder direkt die ID
     * @return void
     */

    public function prepareEmployeeData($id): void
    {
        // Event dispatchen, das EditEmployee abfangen kann
        $this->dispatch('edit-employee', ['userId' => $id]);
    }

    public function editEmployee($userId): void
    {
        try {
            $this->userId = $userId;
            if (!$this->userId) {
                throw new \Exception('Keine gültige Benutzer-ID empfangen');

                Log::error('Keine gültige Benutzer-ID empfangen');
            }

            // Benutzer mit allen relevanten Relationen laden
            $this->user = User::with([
                'employee:id,user_id,employee_status,profession_id,stage_id,supervisor_id',
                'teams:id,name',
                'roles:id,name'
            ])->findOrFail($this->userId);

            // Benutzerdaten in die Komponente laden
            $this->fillFormWithUserData();

            // Modal öffnen und Event-Zustand aktualisieren
            $this->showModal = true;
            $this->dataLoaded = false; // Daten beim Rendern neu laden

            // Informiere UI über das Öffnen des Modals
            $this->dispatch('modal-show', ['name' => 'edit-employee']);
        } catch (\Exception $e) {
            $this->handleError('Fehler beim Laden des Mitarbeiters', $e);
        }
    }

    /**
     * Befüllt das Formular mit den Benutzerdaten
     *
     * @return void
     */
    protected function fillFormWithUserData(): void
    {
        if (!$this->user) {
            return;
        }

        // Zurücksetzen aller relevanten Eigenschaften
        $this->reset([
            'name', 'last_name', 'email', 'gender', 'model_status',
            'joined_at', 'department', 'selectedTeams', 'selectedRoles',
            'employee_status', 'profession', 'stage', 'supervisor'
        ]);

        // Benutzerdaten zuweisen
        $this->name = $this->user->name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->gender = $this->user->gender;
        $this->model_status = $this->user->model_status;
        $this->joined_at = $this->user->joined_at;
        $this->department = $this->user->department_id;

        // Beziehungen zuweisen
        $this->selectedTeams = $this->user->teams->pluck('id')->toArray();
        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();

        // Mitarbeiterdaten zuweisen, falls vorhanden
        if ($this->user->employee) {
            $this->employee_status = $this->user->employee->employee_status;
            $this->profession = $this->user->employee->profession_id;
            $this->stage = $this->user->employee->stage_id;
            $this->supervisor = $this->user->employee->supervisor_id;
        }
    }

    /**
     * Lädt alle für die Formulare benötigten Daten
     *
     * @return void
     */
    protected function loadEssentialData(): void
    {
        if ($this->dataLoaded) {
            return;
        }

        try {
            $currentUserId = auth()->id();
            $currentCompanyId = auth()->user()->company_id;
            $currentTeamId = auth()->user()->current_team_id;

            // Optimiertes Laden der Teams
            $this->cache['teams'] = Team::where('company_id', $currentCompanyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            // Abteilungen basierend auf ausgewähltem Team
            $teamId = !empty($this->selectedTeams) ? $this->selectedTeams[0] : $currentTeamId;
            $this->cache['departments'] = Department::where('model_status', ModelStatus::ACTIVE->value)
                ->where('company_id', $currentCompanyId)
                ->when($teamId, function ($query) use ($teamId) {
                    return $query->where('team_id', $teamId);
                })
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            // Rollen mit optimierter Abfrage
            $this->cache['roles'] = Role::where('access', RoleHasAccessTo::EmployeePanel->value)
                ->where('visible', RoleVisibility::Visible->value)
                ->where(function ($query) use ($currentUserId) {
                    $query->where('created_by', 1)
                        ->orWhere('created_by', $currentUserId);
                })
                ->select(['id', 'name', 'is_manager'])
                ->get();

            // Berufe und Karrierestufen
            $this->cache['professions'] = Profession::where('company_id', $currentCompanyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            $this->cache['stages'] = Stage::where('company_id', $currentCompanyId)
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get();

            // Optimiertes Laden der Vorgesetzten mit besserer Abfrage
            $this->cache['supervisors'] = User::select(['users.id', 'users.name', 'users.last_name', 'users.profile_photo_path'])
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', function($join) {
                    $join->on('model_has_roles.role_id', '=', 'roles.id')
                        ->where('roles.is_manager', true)
                        ->where('model_has_roles.model_type', User::class);
                })
                ->where('users.company_id', $currentCompanyId)
                ->whereNull('users.deleted_at')
                ->when($this->userId, function ($query) {
                    return $query->where('users.id', '!=', $this->userId);
                })
                ->distinct()
                ->get();

            $this->dataLoaded = true;
        } catch (\Exception $e) {
            $this->handleError('Fehler beim Laden der Formulardaten', $e);
            $this->dataLoaded = false;
        }
    }

    /**
     * Lädt die Daten für einen Dropdown bei Bedarf nach
     *
     * @param string $key
     * @return mixed
     */
    protected function getCachedData(string $key)
    {
        if ($this->showModal && (!isset($this->cache[$key]) || $this->cache[$key] === null)) {
            $this->loadEssentialData();
        }

        return $this->cache[$key] ?? collect([]);
    }

    /**
     * Aktualisiert den Mitarbeiter in der Datenbank
     *
     * @return void
     */
    public function updateEmployee(): void
    {
        try {
            $this->validate();

            if (!$this->userId || !$this->user) {
                throw new \Exception('Kein Mitarbeiter zum Aktualisieren ausgewählt.');
            }

            DB::beginTransaction();

            // Benutzerdaten aktualisieren
            $this->updateUserData();

            // Beziehungen aktualisieren
            $this->updateRelationships();

            // Mitarbeiterdaten aktualisieren
            $this->updateEmployeeData();

            DB::commit();

            Flux::toast(
                text: __('Mitarbeiter erfolgreich aktualisiert.'),
                heading: __('Erfolg'),
                variant: 'success'
            );

            // Modal schließen und Event auslösen
            $this->closeModal();
            $this->dispatch('employee-updated');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->handleError('Fehler beim Aktualisieren', $e);
        }
    }

    /**
     * Aktualisiert die Benutzerdaten
     */
    protected function updateUserData(): void
    {
        $this->user->update([
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'gender' => $this->gender,
            'model_status' => $this->model_status,
            'joined_at' => $this->joined_at,
            'department_id' => $this->department,
        ]);
    }

    /**
     * Aktualisiert die Beziehungen des Benutzers
     */
    protected function updateRelationships(): void
    {
        $this->user->teams()->sync($this->selectedTeams);
        $this->user->roles()->sync($this->selectedRoles);

        // Stelle sicher, dass der Benutzer mindestens ein Team hat
        if (empty($this->selectedTeams) && auth()->user()->currentTeam) {
            $this->user->teams()->attach(auth()->user()->currentTeam->id, ['role' => 'member']);
        }
    }

    /**
     * Aktualisiert oder erstellt den Mitarbeiterdatensatz
     */
    protected function updateEmployeeData(): void
    {
        $employeeData = [
            'employee_status' => $this->employee_status,
            'profession_id' => $this->profession,
            'stage_id' => $this->stage,
            'supervisor_id' => $this->supervisor,
        ];

        if ($this->user->employee) {
            $this->user->employee->update($employeeData);
        } else {
            // Falls noch kein Mitarbeiterdatensatz existiert, erstelle einen
            $employeeData['user_id'] = $this->user->id;
            $employeeData['uuid'] = \Illuminate\Support\Str::uuid();
            Employee::create($employeeData);
        }
    }

    /**
     * Behandelt Reaktionen auf Änderungen von Eigenschaften
     *
     * @param string $propertyName
     */
    public function updated($propertyName): void
    {
        // Validierung bei Änderungen ausführen
        // Kann bei Bedarf aktiviert werden, ist aber bei großen Formularen möglicherweise zu aggressiv
        // $this->validateOnly($propertyName);

        // Bei Änderung des Teams müssen die Abteilungen aktualisiert werden
        if ($propertyName === 'selectedTeams') {
            $this->cache['departments'] = null; // Cache für Abteilungen zurücksetzen

            // Wenn ein Team ausgewählt wurde und die aktuelle Abteilung nicht zu diesem Team gehört
            if (!empty($this->selectedTeams) && $this->department) {
                $teamId = $this->selectedTeams[0];
                $departmentExists = Department::where('id', $this->department)
                    ->where('team_id', $teamId)
                    ->exists();

                if (!$departmentExists) {
                    $this->department = null; // Zurücksetzen der Abteilung, wenn sie nicht zum Team gehört
                }
            }

            // Neu laden der Abteilungen für das ausgewählte Team
            $this->loadEssentialData();
        }
    }

    /**
     * Behandelt Fehler einheitlich
     *
     * @param string $message Die Fehlermeldung
     * @param \Exception $exception Die aufgetretene Exception
     * @return void
     */
    protected function handleError(string $message, \Exception $exception): void
    {
        // Fehler loggen
        Log::error("{$message}: " . $exception->getMessage(), [
            'exception' => $exception,
            'user_id' => $this->userId,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        // Benutzer benachrichtigen
        Flux::toast(
            text: __("{$message}: ") . $exception->getMessage(),
            heading: __('Fehler'),
            variant: 'danger'
        );
    }

    /**
     * Schließt das Modal und setzt den Zustand zurück
     */
    public function closeModal(): void
    {
        $this->reset([
            'userId', 'user', 'gender', 'name', 'last_name', 'email',
            'joined_at', 'department', 'selectedTeams', 'selectedRoles',
            'model_status', 'employee_status', 'profession', 'stage', 'supervisor'
        ]);

        $this->showModal = false;
        $this->dataLoaded = false;
        $this->cache = array_fill_keys(array_keys($this->cache), null);

        $this->dispatch('modal-close', ['name' => 'edit-employee']);
    }

    /**
     * Computed Properties für Dropdown-Daten
     */
    #[Computed]
    public function teams()
    {
        return $this->getCachedData('teams');
    }

    #[Computed]
    public function departments()
    {
        return $this->getCachedData('departments');
    }

    #[Computed]
    public function roles()
    {
        return $this->getCachedData('roles');
    }

    #[Computed]
    public function professions()
    {
        return $this->getCachedData('professions');
    }

    #[Computed]
    public function stages()
    {
        return $this->getCachedData('stages');
    }

    #[Computed]
    public function supervisors()
    {
        return $this->getCachedData('supervisors');
    }

    /**
     * Stellt Mitarbeiterstatus-Optionen für das Dropdown bereit
     */
    #[Computed]
    public function employeeStatusOptions()
    {
        return collect(EmployeeStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
                'colors' => $status->colors(),
                'icon' => $status->icon(),
            ];
        })->toArray();
    }

    /**
     * Rendert die Komponente
     *
     * @return View
     */
    public function render(): View
    {
        // Lazy-Loading der Daten beim Anzeigen des Modals
        if ($this->showModal && !$this->dataLoaded) {
            $this->loadEssentialData();
        }

        // Optimierte Weitergabe von Daten an die View
        $viewData = [
            'employeeStatusOptions' => $this->employeeStatusOptions(),
            'modelStatusOptions' => $this->modelStatusOptions,
        ];

        // Lazy-Loading der Dropdown-Daten nur wenn das Modal geöffnet ist
        if ($this->showModal) {
            $viewData = array_merge($viewData, [
                'teams' => $this->teams(),
                'departments' => $this->departments(),
                'roles' => $this->roles(),
                'professions' => $this->professions(),
                'stages' => $this->stages(),
                'supervisors' => $this->supervisors(),
            ]);
        }

        return view('livewire.alem.employee.edit', $viewData);
    }

}
