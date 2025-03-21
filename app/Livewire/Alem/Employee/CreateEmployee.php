<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Employee\Employee;
use App\Models\Spatie\Role;
use App\Models\User;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
//use Carbon\Carbon;
use Illuminate\Support\Carbon;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateEmployee extends Component
{
    use ValidateEmployee, AuthorizesRequests;


    /**
     * Standardwerte für neue Mitarbeiter
     */
    public $selectedRoles = [];         // Employee User kann mehrere Rollen haben
    public $model_status = null;       // Account Status des Benutzers
    public $employee_status = null;    // Beschäftigungs Status des Mitarbeiters

    /**
     * Benutzer-Felder (User Fields)
     */
    public $gender = null;
    public $name;
    public $last_name;
    public $email;
    public $password;
    public ?Carbon $joined_at;

    /**
     * Team-Zuordnung
     */
    public $selectedTeams = [];        // Ausgewählte Teams für den Mitarbeiter

    /**
     * Mitarbeiter-spezifische Felder (Employee Fields)
     */
    public $profession;                // Beruf/Position
    public $stage;                     // Karrierestufe

    /**
     * Benachrichtigungseinstellungen
     */
    public $notifications = false;     // E-Mail-Benachrichtigung senden?
    public bool $isActive = false;     // Konto sofort aktivieren?

    /**
     * Modal control
     */
    public bool $showModal = false;    // Zustand des Modals

    /**
     * Initialisiert die Komponente mit Standardwerten
     */
    public function mount(): void
    {
        $this->model_status = ModelStatus::ACTIVE;
        $this->employee_status = EmployeeStatus::PROBATION;
        $this->gender = Gender::Male;
    }

    //-------------------------------------------------------------------------
    // COMPUTED PROPERTIES (GETTER)
    //-------------------------------------------------------------------------

    /**
     * Lädt die Liste der Berufe/Positionen für das Auswahlfeld
     * Wird aktualisiert wenn das Event 'professionUpdated' ausgelöst wird
     */
    #[On('professionUpdated')]
    public function getProfessionsProperty()
    {
        return Profession::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    /**
     * Lädt die Liste der Karrierestufen für das Auswahlfeld
     * Wird aktualisiert wenn das Event 'stageUpdated' ausgelöst wird
     */
    #[On('stageUpdated')]
    public function getStagesProperty()
    {
        return Stage::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    /**
     * Lädt alle verfügbaren Rollen für Mitarbeiter
     * Wird aktualisiert wenn das Event 'roleUpdated' ausgelöst wird
     */
    #[On('roleUpdated')]
    public function getRolesProperty()
    {
        return Role::where(function ($query) {
            $query->where('access', RoleHasAccessTo::EmployeePanel)
                ->where('visible', RoleVisibility::Visible);
        })
            ->where('created_by', 1)                 // System-erstellte Rollen
            ->orWhere('created_by', auth()->id())    // Oder vom aktuellen Benutzer erstellte Rollen
            ->get();
    }

    /**
     * Lädt alle Teams, auf die der aktuelle Benutzer Zugriff hat
     */
    public function getTeamsProperty()
    {
        return auth()->user()->allTeams();
    }

    //-------------------------------------------------------------------------
    // AKTIONEN (ACTIONS)
    //-------------------------------------------------------------------------

    /**
     * Speichert einen neuen Mitarbeiter in der Datenbank
     *
     * Diese Methode führt folgende Schritte aus:
     * 1. Autorisierung prüfen
     * 2. Formulardaten validieren
     * 3. Benutzer erstellen
     * 4. Rollen zuweisen
     * 5. Mitarbeiter-Datensatz erstellen
     * 6. Teams zuweisen
     * 7. Optional: Benachrichtigung senden
     */
    public function saveEmployee(): void
    {
        // Autorisierung prüfen
        $this->authorize('create', Employee::class);

        // Validierung durchführen (aus dem ValidateEmployee-Trait)
        $this->validate();

        try {
            // 1. Benutzer erstellen
            $user = User::create([
                // Persönliche Daten
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'gender' => $this->gender,

                // Sicherheit und Status
                'password' => Hash::make($this->password),
                'email_verified_at' => $this->isActive ? now() : null,
                'model_status' => $this->model_status ?? ModelStatus::ACTIVE,

                // Organisations-Zugehörigkeit
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
                'joined_at' => $this->joined_at ? Carbon::parse($this->joined_at) : null,

                // Benutzertyp - immer Employee für diese Komponente
                'user_type' => UserType::Employee,
            ]);

            // 2. Rollen zuweisen
            if (!empty($this->selectedRoles)) {
                $user->roles()->sync($this->selectedRoles);
            }

            // 3. Mitarbeiter-Datensatz erstellen
            Employee::create([
                'user_id' => $user->id,
                'uuid' => (string)Str::uuid(),
                'date_hired' => $this->joined_at ? Carbon::parse($this->joined_at) : null,
                'profession' => $this->profession,
                'stage' => $this->stage,
                'company_id' => auth()->user()->company_id,
                'team_id' => auth()->user()->currentTeam->id,
                'created_by' => auth()->id(),
                'employee_status' => $this->employee_status ?? EmployeeStatus::PROBATION,
            ]);

            // 4. Teams zuweisen
            if (!empty($this->selectedTeams)) {
                // Zu allen ausgewählten Teams hinzufügen
                foreach ($this->selectedTeams as $teamId) {
                    $team = auth()->user()->allTeams()->find($teamId);
                    if ($team) {
                        $user->teams()->attach($team, ['role' => 'member']);
                    }
                }
            } else {
                // Falls keine Teams ausgewählt wurden, zum aktuellen Team hinzufügen
                $user->teams()->attach(auth()->user()->currentTeam, ['role' => 'member']);
            }

            // 5. Benachrichtigung senden (falls aktiviert)
            if ($this->notifications) {
                // Hier E-Mail-Benachrichtigung implementieren
                // Mail::to($this->email)->send(new EmployeeInvitation($user));
            }

            // Formular zurücksetzen und Event auslösen
            $this->resetForm();
            $this->dispatch('employeeCreated');

            // Erfolgsmeldung anzeigen
            Flux::toast(
                text: __('Employee created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

        } catch (AuthorizationException $ae) {
            // Berechtigungsfehler
            Flux::toast(
                text: __('You are not authorized to create employees.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            // Allgemeiner Fehler
            Flux::toast(
                text: __('Error: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Setzt das Formular zurück und initialisiert Standardwerte
     */
    public function resetForm(): void
    {
        // Alle Formularfelder zurücksetzen
        $this->reset([
            'name', 'last_name', 'email', 'password', 'gender', 'selectedRoles',
            'joined_at', 'profession', 'stage', 'selectedTeams',
            'model_status', 'employee_status', 'notifications', 'isActive'
        ]);

        // Modal schließen
        $this->showModal = false;

        // Standardwerte neu initialisieren
        $this->model_status = ModelStatus::ACTIVE;
        $this->employee_status = EmployeeStatus::PROBATION;
        $this->selectedRoles = []; // Leeres Array für Rollen
    }

    /**
     * Rendert die Komponentenansicht
     */
    public function render(): View
    {
        return view('livewire.alem.employee.create');
    }
}
