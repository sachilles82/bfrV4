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

// Entferne Auth, wenn nicht mehr direkt benötigt
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Locked;
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
use Illuminate\Support\Collection;

#[Lazy(isolate: false)]
class EditEmployee extends Component
{
    use AuthorizesRequests, ValidateEmployee, WithModelStatusOptions;

    // Modal state
    public bool $showEditModal = false;
    private bool $dataLoadedEdit = false;

    // User identification
    #[Locked]
    public ?int $userId = null;
    public ?User $user = null;

    // Eigenschaften für vorgeladene Daten - diese werden automatisch von Livewire befüllt
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

    // Optimierung: Cache-Eigenschaften privat lassen und initialisieren
    private ?Collection $teams = null;
    private ?Collection $departments = null;
    private ?Collection $roles = null;
    private ?Collection $professions = null;
    private ?Collection $stages = null;
    private ?Collection $supervisors = null;

    #[On('edit-employee')]
    public function editEmployee($userId): void
    {
        try {
            $this->userId = is_array($userId) && isset($userId['userId']) ? $userId['userId'] : $userId;

            if (!$this->userId) {
                throw new \Exception(__('Keine gültige Benutzer-ID empfangen'));
            }

            $this->user = User::with([
                'employee:id,user_id,employee_status,profession_id,stage_id,supervisor_id',
                'teams:id,name',
                'roles:id,name',
                'department:id,name'
            ])->findOrFail($this->userId);

            $this->fillFormWithUserData();

            $this->showEditModal = true;
            $this->dataLoadedEdit = false;

            $this->loadEssentialData();

        } catch (\Exception $e) {
            $this->handleError(__('Fehler beim Laden des Mitarbeiters', $e ->getMessage()), $e);
        }
    }

    /**
     * Fill form with user data
     */
    protected function fillFormWithUserData(): void
    {
        if (!$this->user) return;

        $this->gender = $this->user->gender;
        $this->name = $this->user->name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;

        $this->selectedTeams = $this->user->teams->pluck('id')->toArray();
        $this->department = $this->user->department_id;

        $this->model_status = $this->user->model_status;
        $this->joined_at = $this->user->joined_at;


        $this->selectedRoles = $this->user->roles->pluck('id')->toArray();

        if ($employee = $this->user->employee) {
            $this->employee_status = $employee->employee_status instanceof EmployeeStatus
                ? $employee->employee_status->value
                : $employee->employee_status;
            $this->profession = $employee->profession_id;
            $this->stage = $employee->stage_id;
            $this->supervisor = $employee->supervisor_id;
        } else {
            // Standardwerte falls kein Employee-Datensatz existiert
            $this->employee_status = null;
            $this->profession = null;
            $this->stage = null;
            $this->supervisor = null;
        }
    }

    /**
     * Load all data needed for form dropdowns - uses passed properties
     */
    protected function loadEssentialData(): void
    {

        if (!$this->showEditModal || $this->dataLoadedEdit) {
            return;
        }

        try {
            $currentUserId = $this->authUserId;
            $companyId = $this->companyId;

            if ($this->teams === null) {
                $this->teams = Team::where('company_id', $companyId)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            // TeamScope ist aktiv, filtert automatisch nach Auth::user()->currentTeam->id
            if ($this->departments === null) {
                $this->departments = Department::where('model_status', ModelStatus::ACTIVE->value)
                    ->select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            if ($this->supervisors === null) {
                $this->supervisors = User::query()
                    ->where('company_id', $companyId)
                    ->whereHas('roles', fn($q) => $q->where('is_manager', true))
                    ->select(['id', 'name', 'last_name', 'profile_photo_path'])
                    ->distinct()
                    ->get();
            }

            if ($this->roles === null) {
                $this->roles = Role::where('access', RoleHasAccessTo::EmployeePanel->value)
                    ->where('visible', RoleVisibility::Visible->value)
                    ->whereIn('created_by', [1, $currentUserId])
                    ->select(['id', 'name', 'is_manager'])
                    ->get();
            }

            // CompanyScope ist aktiv, filtert automatisch nach Auth::user()->company_id
            if ($this->professions === null) {
                $this->professions = Profession::select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            // CompanyScope ist aktiv, filtert automatisch nach Auth::user()->company_id
            if ($this->stages === null) {
                $this->stages = Stage::select(['id', 'name'])
                    ->orderBy('name')
                    ->get();
            }

            $this->dataLoadedEdit = true; // Setze das Flag erst, wenn alles versucht wurde (Fehler werden intern behandelt)

        } catch (\Exception $e) {
            $this->handleError('Genereller Fehler beim Laden der Formulardaten', $e);
            $this->dataLoadedEdit = false; // Sicherstellen, dass bei Fehler erneut geladen werden kann
        }
    }



    /**
     * Update employee in database
     */
    public function updateEmployee(): void
    {
        // Konvertiere Enums zu Strings für die Validierung/Speicherung
        $this->employee_status = $this->employee_status instanceof EmployeeStatus ? $this->employee_status->value : $this->employee_status;
        $this->model_status = $this->model_status instanceof ModelStatus ? $this->model_status->value : $this->model_status;

        try {
            // Passe die Validierungsregel für 'supervisor' an, um den aktuellen Benutzer auszuschließen
            $this->rules['supervisor'] = ['required', 'exists:users,id', Rule::notIn([$this->userId])];
            $this->validate();

            if (!$this->userId) {
                throw new \Exception('Kein Mitarbeiter zur Aktualisierung ausgewählt.');
            }

            DB::beginTransaction();

            try {
                // User-Daten aktualisieren
                User::where('id', $this->userId)->update([
                    'name' => $this->name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'gender' => $this->gender,
                    'model_status' => $this->model_status,
                    'joined_at' => $this->joined_at ? Carbon::parse($this->joined_at)->format('Y-m-d') : null,
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

                // Beziehungen aktualisieren (User neu laden, da update() keine Model-Instanz zurückgibt)
                $updatedUser = User::find($this->userId);
                if ($updatedUser) {
                    $updatedUser->roles()->sync($this->selectedRoles);
                    $updatedUser->teams()->sync($this->selectedTeams);
                } else {
                    throw new \Exception('Benutzer nach Update nicht gefunden.');
                }

                DB::commit();

                $this->closeModal(); // Modal schließen und Reset durchführen
                $this->dispatch('employee-updated'); // Event für Tabellen-Refresh
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Mitarbeiter wurde erfolgreich aktualisiert.'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                // Gib den Fehler an die handleError Methode weiter
                $this->handleError('Fehler beim Speichern in der Datenbank', $e);
                // Breche hier ab, da die Transaktion fehlgeschlagen ist
                return;
            }

        } catch (ValidationException $e) {
            // Halte das Modal offen und zeige Validierungsfehler an
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Bitte korrigieren Sie die markierten Felder.'
            ]);
        } catch (\Exception $e) {
            // Bei anderen unerwarteten Fehlern
            DB::rollBack(); // Sicherstellen, dass Rollback erfolgt
            $this->handleError('Unerwarteter Fehler beim Aktualisieren', $e);
        }
    }


    // --- Event Handler für Cache-Invalidierung ---
    #[On('profession-updated')]
    public function refreshProfessions(): void
    {
        $this->professions = null;
        if ($this->showEditModal) $this->loadEssentialData();
    }

    #[On('stage-updated')]
    public function refreshStages(): void
    {
        $this->stages = null;
        if ($this->showEditModal) $this->loadEssentialData();
    }

    #[On(['department-created', 'department-updated'])]
    public function refreshDepartments(): void
    {
        $this->departments = null;
        if ($this->showEditModal) $this->loadEssentialData();
    }

    /**
     * Get employee status options for dropdown - static caching
     */
    public function getEmployeeStatusOptionsProperty(): array
    {
        static $options = null;
        if ($options === null) {
            $options = collect(EmployeeStatus::cases())->map(fn($status) => [
                'value' => $status->value,
                'label' => $status->label(),
                'colors' => $status->colors(),
                'icon' => $status->icon(),
            ])->toArray();
        }
        return $options;
    }

    /**
     * Close modal and reset state
     */
    public function closeModal(): void
    {
        $this->reset([
            'userId', 'user', 'showEditModal', 'dataLoadedEdit',
            'name', 'last_name', 'email', 'gender',
            'model_status', 'joined_at', 'department',
            'selectedTeams', 'selectedRoles',
            'employee_status', 'profession', 'stage', 'supervisor'
        ]);
        $this->resetErrorBag(); // Validierungsfehler zurücksetzen

        // Reset cached data (private properties)
        $this->teams = null;
        $this->departments = null;
        $this->roles = null;
        $this->professions = null;
        $this->stages = null;
        $this->supervisors = null;

        // Close modal via Alpine.js
        $this->dispatch('modal-close-edit', ['name' => 'edit-employee']);
    }

    #[Computed]
    public function teams(): Collection
    {
        return $this->teams ?? collect();
    }

    #[Computed]
    public function departments(): Collection
    {
        return $this->departments ?? collect();
    }

    #[Computed]
    public function roles(): Collection
    {
        return $this->roles ?? collect();
    }

    #[Computed]
    public function professions(): Collection
    {
        return $this->professions ?? collect();
    }

    #[Computed]
    public function property(): Collection
    {
        return $this->stages ?? collect();
    }

    #[Computed]
    public function supervisors(): Collection
    {
        return $this->supervisors ?? collect();
    }

    public function render(): View
    {
        return view('livewire.alem.employee.edit', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'modelStatusOptions' => $this->modelStatusOptions,
        ]);
    }

    /**
     * Handle an error by logging it and showing a notification
     */
    private function handleError(string $message, \Exception $exception): void
    {
        Log::error("{$message}: " . $exception->getMessage(), [
            'exception_message' => $exception->getMessage(), // Umbenannt für Klarheit
            'user_id' => $this->userId,
            'auth_user_id' => $this->authUserId,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString() // Füge Stack Trace hinzu für besseres Debugging
        ]);

        // Sende nur eine generische Fehlermeldung an den Benutzer, außer bei bekannten Problemen
        $this->dispatch('notify', [
            'type' => 'error',
            'message' => $this->getUserFriendlyErrorMessage($exception, $message) // Übergebe ursprüngliche Nachricht
        ]);
    }

    /**
     * Konvertiert technische Fehlermeldungen in benutzerfreundliche Nachrichten
     */
    private function getUserFriendlyErrorMessage(\Exception $exception, string $contextMessage): string
    {
        $technicalMessage = $exception->getMessage();

        // Spezifische, sichere Fehlermeldungen
        if (strpos($technicalMessage, 'Duplicate entry') !== false && strpos($technicalMessage, 'email') !== false) {
            return 'Diese E-Mail-Adresse wird bereits verwendet.';
        }
        if (strpos($technicalMessage, 'foreign key constraint fails') !== false) {
            return 'Ein verknüpfter Datensatz (z.B. Abteilung, Rolle) existiert nicht oder kann nicht zugewiesen werden.';
        }
        if (strpos($technicalMessage, 'Missing essential IDs') !== false) {
            return 'Ein interner Fehler ist aufgetreten (fehlende IDs). Bitte Seite neu laden.';
        }

        // Generische Fehlermeldung für unerwartete Fehler
        Log::warning("Anzeige einer generischen Fehlermeldung für: {$contextMessage} - {$technicalMessage}");
        return 'Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut oder kontaktieren Sie den Support.';
    }

}
