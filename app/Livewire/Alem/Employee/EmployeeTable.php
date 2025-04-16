<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleVisibility;
use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\Searchable;
use App\Livewire\Alem\Employee\Helper\WithEmployeeModelStatus;
use App\Livewire\Alem\Employee\Helper\WithEmployeeSorting;
use App\Livewire\Alem\Employee\Helper\WithEmployeeStatus;
use App\Models\User;
use App\Traits\Table\WithPerPagePagination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class EmployeeTable extends Component
{
    use Searchable, WithEmployeeModelStatus, WithEmployeeSorting,
        WithEmployeeStatus,
        WithPerPagePagination;

    /**
     * Tabelle zeigt nur User mit user_typ employee
     */
    protected string $userType = UserType::Employee->value;

    // Diese Eigenschaften werden vom EmployeeIndexController befüllt
    // und enthalten die Lookup-Daten für Filter etc.
    public array $departments = [];

    public array $teams = [];

    public array $professions = [];

    public array $stages = [];
    // Rollen könnten auch nützlich sein, falls ein Rollenfilter existiert/geplant ist
    // public array $roles = [];

    /**
     * Hört auf das Event 'employee-created', 'employee-updated' und aktualisiert die Tabelle
     */
    #[On(['employee-created', 'employee-updated'])]
    public function refreshTable(): void
    {
        // Setzt die aktuelle Seite zurück, damit man nach einer Änderung nicht auf einer leeren Seite landet
        $this->resetPage();
        // Keine Notwendigkeit, explizit Daten neu zu laden, `render()` wird automatisch aufgerufen.
    }

    /**
     * Lifecycle-Hook: Wird aufgerufen, wenn sich eine öffentliche Eigenschaft ändert.
     * Setzt die Auswahl zurück, wenn Filter geändert werden.
     */
    public function updated($property): void
    {
        // Prüfen, ob sich einer der Filter geändert hat
        if (in_array($property, [
            'search', // Suche hinzugefügt
            'statusFilter',
            'employeeStatusFilter',
            'sortCol', // Sortierung hinzugefügt
            'sortAsc', // Sortierung hinzugefügt
            'perPage', // Seitengrösse hinzugefügt
        ])) {
            // Auswahl aufheben, wenn gefiltert/sortiert/gesucht wird
            $this->selectedIds = [];
            // Seite zurücksetzen, ausser wenn es die Seitengrösse selbst war
            if ($property !== 'perPage') {
                $this->resetPage();
            }
            // Wenn der Hauptstatusfilter geändert wird, den Unterfilter zurücksetzen
            if ($property === 'statusFilter') {
                $this->reset('employeeStatusFilter');
            }
            // Informiert eventuell andere Komponenten (z.B. eine Auswahl-Info-Box)
            $this->dispatch('update-table');
        }
    }

    /**
     * Alle Filter zurücksetzen
     */
    public function resetFilters(): void
    {
        $this->resetPage();
        $this->reset('search', 'sortCol', 'sortAsc', 'statusFilter', 'employeeStatusFilter');
        $this->selectedIds = []; // Auswahl auch hier zurücksetzen
        // Eventuell verbundene JS-Komponenten informieren
        // $this->dispatch('resetFilters'); // Falls benötigt
        // Tabelle neu rendern lassen
        // $this->dispatch('update-table'); // Nicht nötig, updated() und render() regeln das
    }

    /**
     * Öffnet den Bearbeitungsmodus für einen Mitarbeiter (sendet Event)
     */
    public function edit($id): void
    {
        $this->dispatch('edit-employee', $id); // Event für Modal etc.
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        // Hole die ID des aktuellen Teams des angemeldeten Benutzers
        $authCurrentTeamId = Auth::user()?->currentTeam?->id;

        // Starte Basisabfrage mit deinem Hauptmodell
        $query = User::select([
            'users.id',
            'users.department_id',
            'users.name',
            'users.last_name',
            'users.phone_1',
            'users.email',
            'users.joined_at',
            'users.created_at',
            'users.model_status',
            'users.profile_photo_path',
            'users.slug',
            'users.company_id',
            'users.deleted_at',
            // 'users.current_team_id' // Wahrscheinlich nicht direkt benötigt
        ])
            // Filtert standardmässig auf den Typ 'Employee'
            ->where('users.user_type', $this->userType)
            // Standard-Bedingung: aktiver Status und nicht gelöscht
            ->where('users.model_status', ModelStatus::ACTIVE->value)
            ->whereNull('users.deleted_at');

        // Optimierte Abfrage mit JOIN statt EXISTS-Unterabfragen
        $query->join('employees', 'users.id', '=', 'employees.user_id')
            ->join('team_user', function ($join) use ($authCurrentTeamId) {
                $join->on('users.id', '=', 'team_user.user_id')
                    ->where('team_user.team_id', '=', $authCurrentTeamId);
            });

        // Lade notwendige Relationen mit spezifischen Feldern (Eager Loading)
        $query->with([
            // Lade die Employee-Relation mit benötigten Feldern
            'employee' => function ($q_employee) {
                $q_employee->select(['id', 'user_id', 'employee_status', 'profession_id', 'stage_id']);
            },
            // Profession und Stage direkt in der Employee-Relation geladen
            'employee.profession:id,name',
            'employee.stage:id,name',
            // Lade nur ID und Name des Departments - mehr wird nicht benötigt
            'department:id,name',
            // Lade die Rollen des Users (nur ID und Name, gefiltert nach Sichtbarkeit)
            'roles' => function ($q_roles) {
                $q_roles->where('visible', RoleVisibility::Visible->value)
                    ->select('roles.id', 'roles.name');
            },
        ]);

        // Wende Suche, Sortierung und Statusfilter an (aus den Traits)
        $this->applySearch($query); // Wendet den $this->search Filter an
        $this->applySorting($query); // Wendet $this->sortCol und $this->sortAsc an

        // Status-Filter wird nur bei explizitem Wunsch angewendet (falls nicht Active)
        if ($this->statusFilter && $this->statusFilter !== ModelStatus::ACTIVE->value) {
            $query->where('users.model_status', $this->statusFilter);
        }

        // Wende den spezifischen EmployeeStatus-Filter an, falls gesetzt
        if ($this->employeeStatusFilter) {
            // Filtert basierend auf dem Status in der `employees` Tabelle
            $query->where('employees.employee_status', $this->employeeStatusFilter);
        }

        // Entfernt Duplikate, die durch JOINs entstehen könnten
        $query->distinct();

        // Führe die Abfrage aus und paginiere die Ergebnisse
        // Bei Verwendung von JOINs simplePaginate() mit userId als eindeutigem Feld
        $users = $query->simplePaginate(
            $this->perPage,
            ['users.*'], // Nur Felder aus der users-Tabelle auswählen
            'page'
        );

        // Speichere die IDs der aktuell angezeigten Benutzer für die "Alle auswählen"-Checkbox
        $this->idsOnPage = $users->pluck('id')->map(fn($id) => (string)$id)->toArray();

        // Gebe die View zurück und übergebe die Benutzerdaten und Status-Optionen für Filter
        return view('livewire.alem.employee.table', [
            'users' => $users,
            'statuses' => ModelStatus::cases(), // Für den Hauptstatus-Filter (Aktiv, Archiviert, Papierkorb)
            'employeeStatuses' => EmployeeStatus::cases(), // Für den Mitarbeiterstatus-Filter
        ]);
    }

    public function placeholder():View
    {
        return view('livewire.placeholders.employee.index');
    }
}
