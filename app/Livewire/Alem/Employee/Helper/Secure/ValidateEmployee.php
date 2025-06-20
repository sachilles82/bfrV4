<?php

namespace App\Livewire\Alem\Employee\Helper\Secure;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait ValidateEmployee
{
    /**
     * OPTIMIERTE VERSION - Keine DB Queries mehr!
     * ===========================================
     */
    public function rules(): array
    {
        $rules = [
            // ✅ Enum Validierung (keine DB Query)
            'gender' => ['required', new Enum(Gender::class)],
            'model_status' => ['required', new Enum(ModelStatus::class)],
            'status' => ['required', new Enum(EmployeeStatus::class)],

            // ✅ Simple Validierung (keine DB Query)
            'name' => 'required|string|min:3',
            'joined_at' => 'required|date|before_or_equal:today',

            // ✅ Arrays (keine DB Query)
            'selectedTeams' => 'required|array|min:1',
            'selectedRoles' => 'required|array|min:1',
        ];

        // ❌ ALT: 'email' => Rule::unique()->ignore() macht IMMER eine Query!
        // ✅ NEU: Nur checken wenn Email geändert wurde
        if ($this->emailHasChanged()) {
            $rules['email'] = ['required', 'email', Rule::unique('users', 'email')->ignore($this->userId)];
        } else {
            $rules['email'] = 'required|email';
        }

        // ❌ ALT: exists:table,id macht für JEDES Feld eine Query!
        // ✅ NEU: Rule::in() mit bereits geladenen Daten

        // Department - Nutze geladene Dropdown Daten
        $rules['department'] = ['required', Rule::in($this->getValidDepartmentIds())];

        // Teams - Nutze geladene Daten
        $rules['selectedTeams.*'] = Rule::in(array_column($this->teams, 'id'));

        // Roles - Nutze geladene Daten
        $rules['selectedRoles.*'] = Rule::in(array_column($this->roles, 'id'));

        // Profession - Nutze geladene Daten
        $rules['profession'] = ['required', Rule::in(array_column($this->professions, 'id'))];

        // Stage - Nutze geladene Daten
        $rules['stage'] = ['required', Rule::in(array_column($this->stages, 'id'))];

        // Supervisor - Spezielle Validierung
        $rules['supervisor'] = ['required', 'integer',
            Rule::in($this->getValidSupervisorIds()),
            function ($attribute, $value, $fail) {
                if ($value == $this->userId) {
                    $fail(__('You cannot be your own supervisor.'));
                }
            }
        ];

        // Invitation nur bei Create
        if ($this->isCreateMode() && property_exists($this, 'invitation')) {
            $rules['invitation'] = ['required', 'boolean'];
        }

        return $rules;
    }

    /**
     * HELPER METHODEN
     * ===============
     */

    /**
     * Check ob Email geändert wurde
     */
    public function emailHasChanged(): bool
    {
        // Für Create Mode
        if (!isset($this->userId) || !$this->userId) {
            return true;
        }

        // Für Edit Mode - vergleiche mit Original
        return $this->email !== $this->originalEmail;
    }

    /**
     * Hole valide Department IDs basierend auf selected Teams
     */
    private function getValidDepartmentIds(): array
    {
        // Die Custom Validation für Department/Team Beziehung
        // OHNE extra DB Query!

        // Annahme: departments haben team_id in den Daten
        return collect($this->departments)
            ->filter(function ($dept) {
                // Wenn department keine team_id hat, ist es global
                if (!isset($dept['team_id'])) return true;

                // Sonst muss team_id in selectedTeams sein
                return in_array($dept['team_id'], $this->selectedTeams);
            })
            ->pluck('id')
            ->toArray();
    }

    /**
     * Hole valide Supervisor IDs (ohne sich selbst)
     */
    private function getValidSupervisorIds(): array
    {
        $supervisorIds = array_column($this->supervisors, 'id');

        // Entferne current user ID
        return array_diff($supervisorIds, [$this->userId]);
    }

    /**
     * Helper für Create/Edit Mode
     */
    protected function isCreateMode(): bool
    {
        return !isset($this->userId) || $this->userId === null;
    }

    /**
     * Messages bleiben gleich
     */
    public function messages(): array
    {
        return [
            // User field messages
            'gender.required' => __('Gender is required.'),
            'gender.enum' => __('The selected gender is invalid.'),

            'name.required' => __('Employee name is required.'),
            'name.string' => __('Employee name must be a string.'),
            'name.min' => __('Employee name must be at least 3 characters.'),

            'email.required' => __('Email is required.'),
            'email.email' => __('Email must be a valid email address.'),
            'email.unique' => __('This email address is already in use.'),

            'model_status.required' => __('Account status is required.'),
            'model_status.enum' => __('The selected account status is invalid.'),

            'joined_at.required' => __('Joined date is required.'),
            'joined_at.date' => __('Joined date must be a valid date.'),
            'joined_at.before_or_equal' => __('Joined date cannot be in the future.'),

            'department.required' => __('Department is required.'),
            'department.in' => __('The selected department is invalid.'),

            'selectedTeams.required' => __('At least one team must be selected.'),
            'selectedTeams.array' => __('Teams must be provided as a list.'),
            'selectedTeams.min' => __('Please select at least one team.'),
            'selectedTeams.*.in' => __('One of the selected teams is invalid.'),

            'selectedRoles.required' => __('At least one role must be selected.'),
            'selectedRoles.array' => __('Roles must be provided as a list.'),
            'selectedRoles.min' => __('Please select at least one role.'),
            'selectedRoles.*.in' => __('One of the selected roles is invalid.'),

            'status.required' => __('Employee status is required.'),
            'status.enum' => __('The selected employee status is invalid.'),

            'profession.required' => __('Profession is required.'),
            'profession.in' => __('The selected profession is invalid.'),

            'stage.required' => __('Stage is required.'),
            'stage.in' => __('The selected stage is invalid.'),

            'supervisor.required' => __('Supervisor is required.'),
            'supervisor.integer' => __('Supervisor ID must be an integer.'),
            'supervisor.in' => __('The selected supervisor is invalid.'),

            'invitation.required' => __('The invitation setting is required.'),
            'invitation.boolean' => __('The invitation setting must be true or false.'),
        ];
    }
}
