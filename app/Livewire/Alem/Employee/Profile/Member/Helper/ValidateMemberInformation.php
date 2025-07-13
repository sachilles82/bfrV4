<?php

namespace App\Livewire\Alem\Employee\Profile\Member\Helper;

use App\Enums\Employee\EmployeeStatus;
use Illuminate\Validation\Rule;

trait ValidateMemberInformation
{
    /**
     * Validiert nur die geänderten Felder
     * @return array Die validierten Daten
     */
    public function validateOnlyChanged(): array
    {
        $rules = $this->getChangedFieldRules();

        if (empty($rules)) {
            return [];
        }

        return $this->validate($rules);
    }

    /**
     * Gibt nur die Regeln für geänderte Felder zurück
     */
    private function getChangedFieldRules(): array
    {
        $rules = [];

        // Teams
        if ($this->teamsHaveChanged()) {
            $rules['selectedTeams'] = ['required', 'array', 'min:1'];
            $rules['selectedTeams.*'] = [
                'integer',
                Rule::in(array_column($this->teams, 'id'))
            ];
        }

        // Department
        if ($this->departmentHasChanged()) {
            $rules['department'] = [
                'required',
                'integer',
                Rule::in(array_column($this->departments, 'id'))
            ];
        }

        // Profession
        if ($this->professionHasChanged()) {
            $rules['profession'] = [
                'required',
                'integer',
                Rule::in(array_column($this->professions ?? [], 'id'))
            ];
        }

        // Stage
        if ($this->stageHasChanged()) {
            $rules['stage'] = [
                'required',
                'integer',
                Rule::in(array_column($this->stages ?? [], 'id'))
            ];
        }

        // Supervisor
        if ($this->supervisorHasChanged()) {
            $rules['supervisor'] = [
                'required',
                'integer',
                Rule::in(array_column($this->supervisors, 'id'))
            ];
        }

        // Roles
        if ($this->rolesHaveChanged()) {
            $rules['selectedRoles'] = ['required', 'array', 'min:1'];
            $rules['selectedRoles.*'] = [
                'integer',
                Rule::in(array_column($this->roles, 'id'))
            ];
        }

        // Status
        if ($this->statusHasChanged()) {
            $rules['status'] = ['required', Rule::enum(EmployeeStatus::class)];
        }

        return $rules;
    }

    /**
     * Legacy: Alle Validierungsregeln (für Backward Compatibility)
     */
    public function rules(): array
    {
        return [
            'selectedTeams' => ['required', 'array', 'min:1'],
            'selectedTeams.*' => [
                'integer',
                Rule::in(array_column($this->teams, 'id'))
            ],
            'department' => [
                'required',
                'integer',
                Rule::in(array_column($this->departments, 'id'))
            ],
            'profession' => [
                'required',
                'integer',
                Rule::in(array_column($this->professions ?? [], 'id'))
            ],
            'stage' => [
                'required',
                'integer',
                Rule::in(array_column($this->stages ?? [], 'id'))
            ],
            'supervisor' => [
                'required',
                'integer',
                Rule::in(array_column($this->supervisors, 'id'))
            ],
            'selectedRoles' => ['required', 'array', 'min:1'],
            'selectedRoles.*' => [
                'integer',
                Rule::in(array_column($this->roles, 'id'))
            ],
            'status' => ['required', Rule::enum(EmployeeStatus::class)],
        ];
    }

    /**
     * Validierungs-Nachrichten
     */
    public function messages(): array
    {
        return [
            // Status
            'status.required' => __('Employee status is required.'),
            'status.enum' => __('The selected employee status is invalid.'),

            // Department
            'department.required' => __('Department is required.'),
            'department.integer' => __('Department must be a number.'),
            'department.in' => __('The selected department does not exist.'),

            // Profession
            'profession.required' => __('The profession is required.'),
            'profession.integer' => __('The profession must be a number.'),
            'profession.in' => __('The selected profession is invalid.'),

            // Stage
            'stage.required' => __('The stage is required.'),
            'stage.integer' => __('The stage must be a number.'),
            'stage.in' => __('The selected stage is invalid.'),

            // Supervisor
            'supervisor.required' => __('Supervisor is required.'),
            'supervisor.integer' => __('Supervisor must be a number.'),
            'supervisor.in' => __('The selected supervisor does not exist.'),

            // Teams
            'selectedTeams.required' => __('At least one team must be selected.'),
            'selectedTeams.array' => __('Teams must be provided as a list.'),
            'selectedTeams.min' => __('Please select at least one team.'),
            'selectedTeams.*.integer' => __('Team ID must be a number.'),
            'selectedTeams.*.in' => __('One of the selected teams is invalid.'),

            // Roles
            'selectedRoles.required' => __('At least one role must be selected.'),
            'selectedRoles.array' => __('Roles must be provided as a list.'),
            'selectedRoles.min' => __('Please select at least one role.'),
            'selectedRoles.*.integer' => __('Role ID must be a number.'),
            'selectedRoles.*.in' => __('One of the selected roles is invalid.'),
        ];
    }

    /**
     * Gibt alle geänderten Felder mit ihren Änderungen zurück
     * Nützlich für Logging oder Debugging
     */
    public function getChangedFields(): array
    {
        $changed = [];

        if ($this->departmentHasChanged()) {
            $changed['department_id'] = [
                'old' => $this->originalData['department_id'] ?? null,
                'new' => $this->department
            ];
        }

        if ($this->professionHasChanged()) {
            $changed['profession_id'] = [
                'old' => $this->originalData['profession_id'] ?? null,
                'new' => $this->profession
            ];
        }

        if ($this->stageHasChanged()) {
            $changed['stage_id'] = [
                'old' => $this->originalData['stage_id'] ?? null,
                'new' => $this->stage
            ];
        }

        if ($this->supervisorHasChanged()) {
            $changed['supervisor_id'] = [
                'old' => $this->originalData['supervisor_id'] ?? null,
                'new' => $this->supervisor
            ];
        }

        if ($this->statusHasChanged()) {
            $changed['status'] = [
                'old' => $this->originalData['status'] ?? null,
                'new' => $this->status
            ];
        }

        if ($this->teamsHaveChanged()) {
            $changed['teams'] = [
                'old' => $this->originalData['teamIds'] ?? [],
                'new' => $this->selectedTeams
            ];
        }

        if ($this->rolesHaveChanged()) {
            $changed['roles'] = [
                'old' => $this->originalData['roleIds'] ?? [],
                'new' => $this->selectedRoles
            ];
        }

        return $changed;
    }

    /**
     * Helper: Check ob Department geändert wurde
     */
    private function departmentHasChanged(): bool
    {
        return $this->department !== ($this->originalData['department_id'] ?? null);
    }

    /**
     * Helper: Check ob Profession geändert wurde
     */
    private function professionHasChanged(): bool
    {
        return $this->profession !== ($this->originalData['profession_id'] ?? null);
    }

    /**
     * Helper: Check ob Stage geändert wurde
     */
    private function stageHasChanged(): bool
    {
        return $this->stage !== ($this->originalData['stage_id'] ?? null);
    }

    /**
     * Helper: Check ob Supervisor geändert wurde
     */
    private function supervisorHasChanged(): bool
    {
        return $this->supervisor !== ($this->originalData['supervisor_id'] ?? null);
    }

    /**
     * Helper: Check ob Status geändert wurde
     */
    private function statusHasChanged(): bool
    {
        return $this->status !== ($this->originalData['status'] ?? null);
    }

    /**
     * Generische Methode zum Vergleich von Integer-Arrays
     * Normalisiert beide Arrays (sortiert, unique, nur integers) vor dem Vergleich
     */
    private function intArraysHaveChanged(array $original, array $current): bool
    {
        $normalizedOriginal = $this->normalizeIntArray($original);
        $normalizedCurrent = $this->normalizeIntArray($current);
        return !$this->arraysAreEqual($normalizedOriginal, $normalizedCurrent);
    }

    /**
     * Normalisiere Integer Array (sortiert, unique, nur integers)
     */
    private function normalizeIntArray(array $array): array
    {
        $normalized = array_map('intval', array_filter($array, 'is_numeric'));
        $normalized = array_unique($normalized);
        sort($normalized);
        return array_values($normalized);
    }

    /**
     * Vergleiche zwei Arrays nach Normalisierung
     */
    private function arraysAreEqual(array $array1, array $array2): bool
    {
        return $array1 === $array2;
    }

    /**
     * Helper: Check ob Teams geändert wurden
     */
    private function teamsHaveChanged(): bool
    {
        return $this->intArraysHaveChanged(
            $this->originalData['teamIds'] ?? [],
            $this->selectedTeams
        );
    }

    /**
     * Helper: Check ob Roles geändert wurden
     */
    private function rolesHaveChanged(): bool
    {
        return $this->intArraysHaveChanged(
            $this->originalData['roleIds'] ?? [],
            $this->selectedRoles
        );
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->departmentHasChanged() ||
            $this->professionHasChanged() ||
            $this->stageHasChanged() ||
            $this->supervisorHasChanged() ||
            $this->statusHasChanged() ||
            $this->teamsHaveChanged() ||
            $this->rolesHaveChanged();
    }
}


