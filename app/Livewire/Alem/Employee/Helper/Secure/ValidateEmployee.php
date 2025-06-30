<?php

namespace App\Livewire\Alem\Employee\Helper\Secure;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rule;

trait ValidateEmployee
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

        // Gender
        if ($this->genderHasChanged()) {
            $rules['gender'] = ['required', Rule::enum(Gender::class)];
        }

        // Name
        if ($this->nameHasChanged()) {
            $rules['name'] = 'required|string|min:3|max:255';
        }

        // Email
        if ($this->emailHasChanged()) {
            $rules['email'] = [
                'required',
                'email:rfc,dns,spoof',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId)
            ];
        }

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

        // Profession
        if ($this->professionHasChanged()) {
            $rules['profession'] = [
                'required',
                'integer',
                Rule::in(array_column($this->professions, 'id'))
            ];
        }

        // Stage
        if ($this->stageHasChanged()) {
            $rules['stage'] = [
                'required',
                'integer',
                Rule::in(array_column($this->stages, 'id'))
            ];
        }

        // Joined At
        if ($this->joinedAtHasChanged()) {
            $rules['joined_at'] = 'required|date|before_or_equal:today';
        }

        // Status
        if ($this->statusHasChanged()) {
            $rules['status'] = ['required', Rule::enum(EmployeeStatus::class)];
        }

        // Model Status
        if ($this->modelStatusHasChanged()) {
            $rules['model_status'] = ['required', Rule::enum(ModelStatus::class)];
        }

        return $rules;
    }

    /**
     * Legacy: Alle Validierungsregeln (für Backward Compatibility)
     */
    public function rules(): array
    {
        return [
            'gender' => ['required', Rule::enum(Gender::class)],
            'name' => 'required|string|min:3|max:255',
            'email' => [
                'required',
                'email:rfc,dns,spoof',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->userId)
            ],
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
            'profession' => [
                'required',
                'integer',
                Rule::in(array_column($this->professions, 'id'))
            ],
            'stage' => [
                'required',
                'integer',
                Rule::in(array_column($this->stages, 'id'))
            ],
            'joined_at' => 'required|date|before_or_equal:today',
            'status' => ['required', Rule::enum(EmployeeStatus::class)],
            'model_status' => ['required', Rule::enum(ModelStatus::class)]
        ];
    }

    /**
     * Validierungs-Nachrichten
     */
    public function messages(): array
    {
        return [
            // Gender
            'gender.required' => __('Gender is required.'),
            'gender.enum' => __('The selected gender is invalid.'),

            // Name
            'name.required' => __('Employee name is required.'),
            'name.string' => __('Employee name must be a string.'),
            'name.min' => __('Employee name must be at least 3 characters.'),
            'name.max' => __('Employee name must not exceed 255 characters.'),

            // Email
            'email.required' => __('Email is required.'),
            'email.email' => __('Email must be a valid email address.'),
            'email.max' => __('Email must not exceed 255 characters.'),
            'email.unique' => __('This email address is already in use.'),

            // Department
            'department.required' => __('Department is required.'),
            'department.integer' => __('Department must be a number.'),
            'department.in' => __('The selected department does not exist.'),

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

            // Profession
            'profession.required' => __('Profession is required.'),
            'profession.integer' => __('Profession must be a number.'),
            'profession.in' => __('The selected profession does not exist.'),

            // Stage
            'stage.required' => __('Stage is required.'),
            'stage.integer' => __('Stage must be a number.'),
            'stage.in' => __('The selected stage does not exist.'),

            // Joined At
            'joined_at.required' => __('Joined date is required.'),
            'joined_at.date' => __('Joined date must be a valid date.'),
            'joined_at.before_or_equal' => __('Joined date cannot be in the future.'),

            // Status
            'status.required' => __('Employee status is required.'),
            'status.enum' => __('The selected employee status is invalid.'),

            // Model Status
            'model_status.required' => __('Account status is required.'),
            'model_status.enum' => __('The selected account status is invalid.'),
        ];
    }

    /**
     * Gibt alle geänderten Felder mit ihren Änderungen zurück
     * Nützlich für Logging oder Debugging
     */
    public function getChangedFields(): array
    {
        $changed = [];

        if ($this->genderHasChanged()) {
            $changed['gender'] = [
                'old' => $this->originalData['gender'] ?? null,
                'new' => $this->gender
            ];
        }

        if ($this->nameHasChanged()) {
            $changed['name'] = [
                'old' => $this->originalData['name'] ?? null,
                'new' => $this->name
            ];
        }

        if ($this->emailHasChanged()) {
            $changed['email'] = [
                'old' => $this->originalData['email'] ?? null,
                'new' => $this->email
            ];
        }

        if ($this->departmentHasChanged()) {
            $changed['department_id'] = [
                'old' => $this->originalData['department_id'] ?? null,
                'new' => $this->department
            ];
        }

        if ($this->supervisorHasChanged()) {
            $changed['supervisor_id'] = [
                'old' => $this->originalData['supervisor_id'] ?? null,
                'new' => $this->supervisor
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

        if ($this->professionHasChanged()) {
            $changed['profession'] = [
                'old' => $this->originalData['profession_id'] ?? null,
                'new' => $this->profession
            ];
        }

        if ($this->stageHasChanged()) {
            $changed['stage'] = [
                'old' => $this->originalData['stage_id'] ?? null,
                'new' => $this->stage
            ];
        }

        if ($this->joinedAtHasChanged()) {
            $changed['joined_at'] = [
                'old' => $this->originalData['joined_at'] ?? null,
                'new' => $this->joined_at
            ];
        }

        if ($this->statusHasChanged()) {
            $changed['status'] = [
                'old' => $this->originalData['status'] ?? null,
                'new' => $this->status
            ];
        }

        if ($this->modelStatusHasChanged()) {
            $changed['model_status'] = [
                'old' => $this->originalData['model_status'] ?? null,
                'new' => $this->model_status
            ];
        }


        return $changed;
    }

    /**
     * Helper: Check ob Gender geändert wurde
     */
    private function genderHasChanged(): bool
    {
        return $this->gender !== ($this->originalData['gender'] ?? '');
    }

    /**
     * Helper: Check ob Name geändert wurde
     */
    private function nameHasChanged(): bool
    {
        return $this->name !== ($this->originalData['name'] ?? '');
    }

    /**
     * Helper: Check ob Email geändert wurde
     */
    private function emailHasChanged(): bool
    {
        return $this->email !== ($this->originalData['email'] ?? '');
    }

    /**
     * Helper: Check ob Department geändert wurde
     */
    private function departmentHasChanged(): bool
    {
        return $this->department !== ($this->originalData['department_id'] ?? null);
    }

    /**
     * Helper: Check ob Supervisor geändert wurde
     */
    private function supervisorHasChanged(): bool
    {
        return $this->supervisor !== ($this->originalData['supervisor_id'] ?? null);
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
     * Helper: Check ob Joined At geändert wurde
     */
    private function joinedAtHasChanged(): bool
    {
        return $this->joined_at !== ($this->originalData['joined_at'] ?? null);
    }

    /**
     * Helper: Check ob Status geändert wurde
     */
    private function statusHasChanged(): bool
    {
        return $this->status !== ($this->originalData['status'] ?? '');
    }

    /**
     * Helper: Check ob Model Status geändert wurde
     */
    private function modelStatusHasChanged(): bool
    {
        return $this->model_status !== ($this->originalData['model_status'] ?? '');
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
     * Validiere einzelnes Feld on-the-fly (z.B. wire:blur)
     */
    public function validateField(string $fieldName): void
    {
        $fieldMapping = [
            'gender' => 'genderHasChanged',
            'name' => 'nameHasChanged',
            'email' => 'emailHasChanged',
            'selectedTeams' => 'teamsHaveChanged',
            'selectedRoles' => 'rolesHaveChanged',
            'department' => 'departmentHasChanged',
            'supervisor' => 'supervisorHasChanged',
            'profession' => 'professionHasChanged',
            'stage' => 'stageHasChanged',
            'joined_at' => 'joinedAtHasChanged',
            'status' => 'statusHasChanged',
            'model_status' => 'modelStatusHasChanged',
        ];

        // Prüfe ob das Feld geändert wurde
        if (isset($fieldMapping[$fieldName])) {
            $method = $fieldMapping[$fieldName];
            if (!$this->$method()) {
                // Feld nicht geändert - keine Validierung
                return;
            }
        }

        // Hole nur die Regel für dieses Feld
        $rules = $this->getChangedFieldRules();
        if (isset($rules[$fieldName])) {
            $this->validateOnly($fieldName, [$fieldName => $rules[$fieldName]]);
        }
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->genderHasChanged() ||
            $this->nameHasChanged() ||
            $this->emailHasChanged() ||
            $this->departmentHasChanged() ||
            $this->supervisorHasChanged() ||
            $this->professionHasChanged() ||
            $this->stageHasChanged() ||
            $this->joinedAtHasChanged() ||
            $this->statusHasChanged() ||
            $this->modelStatusHasChanged() ||
            $this->teamsHaveChanged() ||
            $this->rolesHaveChanged();
    }
}
