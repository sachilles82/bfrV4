<?php

namespace App\Livewire\Alem\Employee\Profile;

use App\Enums\Model\ModelStatus;
use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Livewire\Alem\Employee\Profile\Helper\ValidateInformation;
use App\Models\Alem\Department;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Information extends Component
{
    use ValidateInformation, AuthorizesRequests;

    // Der User-Datensatz
    public User $user;

    // Lokale Properties für User-Daten
    public ?string $gender = '';
    public string $name;
    public string $last_name;
    public string $email;
    public ?string $phone_1 = '';
    public $model_status = '';
    public $department = '';

    // Team-Verwaltung - als Array für multiple Teams
    public array $selectedTeams = [];

    // Rolle - einzelne Rolle für den Benutzer
    public $role = '';

    /**
     * Der Mount-Hook erhält einen User.
     */
    public function mount(User $user): void
    {
        // Eager load teams, roles, and department to avoid N+1 issues
        $user->load(['ownedTeams', 'teams', 'roles', 'department']);

        $this->user = $user;

        // Setze die lokalen Felder aus den User-Daten
        $this->name = $user->name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->gender = $user->gender ?? '';
        $this->phone_1 = $user->phone_1 ?? '';
        $this->model_status = $user->model_status ?? ModelStatus::ACTIVE->value;
        $this->department = $user->department_id ?? ''; // Setze department ID

        // Setze die ausgewählten Teams basierend auf den Teams des Users
        $this->selectedTeams = $user->teams->pluck('id')->toArray();

        // Setze die aktuelle Rolle des Benutzers
        $this->role = $user->roles->first()?->id ?? '';
    }

    /**
     * Get available teams for the dropdown
     */
    public function getAvailableTeamsProperty()
    {
        // Alle verfügbaren Teams laden
        return auth()->user()->allTeams();
    }

    /**
     * Get available departments for the dropdown
     */
    public function getDepartmentsProperty()
    {
        // Alle aktiven Departments laden, die zum aktuellen Team gehören
        // Filtere nach aktuellem Team, falls ein Team ausgewählt ist
        $teamId = !empty($this->selectedTeams) ? $this->selectedTeams[0] : null;

        $query = Department::where('model_status', ModelStatus::ACTIVE->value);

        if ($teamId) {
            $query->where('team_id', $teamId);
        }

        return $query->get();
    }

    #[On('roleUpdated')]
    public function getAvailableRolesProperty()
    {
        return \App\Models\Spatie\Role::where(function ($query) {
            $query->where('access', RoleHasAccessTo::EmployeePanel)
                ->where('visible', RoleVisibility::Visible);
        })
            ->where('created_by', 1)                 // System-erstellte Rollen
            ->orWhere('created_by', auth()->id())    // Oder vom aktuellen Benutzer erstellte Rollen
            ->get();
    }

    /**
     * Hilfsmethode, um alle ModelStatus-Optionen für das Dropdown zu erhalten
     */
    public function getModelStatusOptionsProperty()
    {
        return collect(ModelStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
                'dotColor' => $status->dotColor(),
                'icon' => $status->icon()
            ];
        });
    }

    /**
     * Aktualisiert die Daten des Users, die Team-Zugehörigkeit und die Rolle.
     */
    public function updateEmployee(): void
    {
//        $this->authorize('update', $this->user);

        $this->validate();

        try {
            // Update der User-Daten
            $this->user->update([
                'gender' => $this->gender,
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone_1' => $this->phone_1,
                'model_status' => $this->model_status,
                'department_id' => $this->department, // Department aktualisieren
            ]);

            // Verwalte Team-Zugehörigkeiten
            $this->syncUserTeams();

            // Aktualisiere die Rolle des Benutzers
            $this->syncUserRole();

            // Falls das aktuelle Team entfernt wurde, muss der User ein neues aktuelles Team haben
            if (!in_array($this->user->currentTeam?->id, $this->selectedTeams) && !empty($this->selectedTeams)) {
                $teamToSwitch = $this->user->teams()->find($this->selectedTeams[0]);
                if ($teamToSwitch) {
                    $this->user->switchTeam($teamToSwitch);
                }
            }

            Flux::toast(
                text: __('User updated successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->dispatch('update-table');

        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error updating user: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Synchronisiert die ausgewählten Teams mit den User-Teams
     */
    private function syncUserTeams(): void
    {
        // Synchronisiere die Team-Zugehörigkeiten
        $this->user->teams()->sync($this->selectedTeams);
    }

    /**
     * Aktualisiert die Rolle des Benutzers
     */
    private function syncUserRole(): void
    {
        if ($this->role) {
            // Best Practice: Verwende Rollennamen statt IDs wenn möglich
            $roleName = Role::find($this->role)?->name;

            if ($roleName) {
                // syncRoles mit Rollennamen ist am zuverlässigsten
                $this->user->syncRoles([$roleName]);
            }
        } else {
            // Alle Rollen entfernen, wenn keine ausgewählt ist
            $this->user->syncRoles([]);
        }
    }

    public function render(): View
    {
        return view('livewire.alem.employee.profile.information');
    }
}
