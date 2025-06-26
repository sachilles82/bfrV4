<?php

namespace App\Livewire\Alem\Employee\Profile\Account\Helper;

use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use Illuminate\Validation\Rule;

trait ValidateAccountDetails
{
    /**
     * Definiert die Validierungsregeln basierend auf geänderten Daten
     * Nutzt Laravel 11+ Features für bessere Performance
     */
    public function rules(): array
    {
        $rules = [];

        // Gender - Nur validieren wenn geändert
        if ($this->genderHasChanged()) {
            $rules['gender'] = ['required', Rule::enum(Gender::class)];
        } else {
            // Minimale Validierung
            $rules['gender'] = 'required|string';
        }

        // Name - Nur validieren wenn geändert
        if ($this->nameHasChanged()) {
            $rules['name'] = 'required|string|min:3|max:255';
        } else {
            // Minimale Validierung
            $rules['name'] = 'required|string';
        }

        // Email - Unique Check nur wenn geändert
        if ($this->emailHasChanged()) {
            $rules['email'] = [
                'required',
                'email:rfc,dns,spoof',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->employeeId)
            ];
        } else {
            // Basis-Validierung ohne DB-Check
            $rules['email'] = 'required|email:rfc|max:255';
        }

        // Teams - Erweiterte Validierung nur wenn geändert
        if ($this->teamsHaveChanged()) {
            $rules['selectedTeams'] = ['required', 'array', 'min:1'];
            $rules['selectedTeams.*'] = [
                'integer',
                Rule::in(array_column($this->teams, 'id'))  // Nutze geladene Teams
            ];
        } else {
            // Minimale Validierung
            $rules['selectedTeams'] = ['required', 'array', 'min:1'];
            $rules['selectedTeams.*'] = 'integer';
        }


        // Department - Nur validieren wenn geändert
        if ($this->departmentHasChanged()) {
            $rules['department'] = [
                'required',
                'integer',
                Rule::in(array_column($this->departments, 'id'))
            ];
        } else {
            // Minimale Validierung ohne Rule::in Check
            $rules['department'] = 'required|integer';
        }

        // Roles - Erweiterte Validierung nur wenn geändert
        if ($this->rolesHaveChanged()) {
            $rules['selectedRoles'] = ['required', 'array', 'min:1'];
            $rules['selectedRoles.*'] = [
                'integer',
                Rule::in(array_column($this->roles, 'id'))  // Nutze geladene Rollen statt DB-Query
            ];
        } else {
            // Minimale Validierung
            $rules['selectedRoles'] = ['required', 'array', 'min:1'];
            $rules['selectedRoles.*'] = 'integer';  // Nur Integer-Check wenn nicht geändert
        }

        // Wenn du unterscheiden willst zwischen "neu eingegeben" und "geändert":
        if ($this->phoneHasChanged()) {
            if (empty($this->phone_1)) {
                // Phone wurde gelöscht - keine Validierung nötig
            } else {
                // Phone wurde eingegeben/geändert - validieren
                $rules['phone_1'] = [
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^[\d\s\-\+\(\)]+$/'
                ];
            }
        }

        // Model Status - Nur validieren wenn geändert
        if ($this->modelStatusHasChanged()) {
            $rules['model_status'] = ['required', Rule::enum(ModelStatus::class)];
        } else {
            // Minimale Validierung
            $rules['model_status'] = 'required|string';
        }

        return $rules;
    }

    /**
     * Validierungs-Nachrichten
     */
    public function messages(): array
    {
        return [
            // Name
            'name.required' => __('Employee name is required.'),
            'name.string' => __('Employee name must be a string.'),
            'name.min' => __('Employee name must be at least 3 characters.'),
            'name.max' => __('Employee name must not exceed 255 characters.'),

            // Gender
            'gender.required' => __('Gender is required.'),
            'gender.string' => __('Gender must be a string.'),
            'gender.in' => __('The selected gender is invalid.'),

            // Email
            'email.required' => __('Email is required.'),
            'email.email' => __('Email must be a valid email address.'),
            'email.max' => __('Email must not exceed 255 characters.'),
            'email.unique' => __('This email address is already in use.'),

            // Phone
            'phone_1.string' => __('Phone number must be a string.'),
            'phone_1.max' => __('Phone number must not exceed 20 characters.'),
            'phone_1.regex' => __('Phone number format is invalid.'),

            // Model Status
            'model_status.required' => __('Account status is required.'),
            'model_status.string' => __('Account status must be a string.'),
            'model_status.in' => __('The selected account status is invalid.'),

            // Department
            'department.required' => __('Department is required.'),
            'department.integer' => __('Department must be a number.'),
            'department.exists' => __('The selected department does not exist.'),

            // Teams
            'selectedTeams.required' => __('At least one team must be selected.'),
            'selectedTeams.array' => __('Teams must be provided as a list.'),
            'selectedTeams.min' => __('Please select at least one team.'),
            'selectedTeams.*.integer' => __('Team ID must be a number.'),
            'selectedTeams.*.exists' => __('One of the selected teams is invalid.'),

            // Roles
            'selectedRoles.required' => __('At least one role must be selected.'),
            'selectedRoles.array' => __('Roles must be provided as a list.'),
            'selectedRoles.min' => __('Please select at least one role.'),
            'selectedRoles.*.integer' => __('Role ID must be a number.'),
            'selectedRoles.*.exists' => __('One of the selected roles is invalid.'),
        ];
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
     * Helper: Check ob Teams geändert wurden
     */
    private function teamsHaveChanged(): bool
    {
        $originalTeamIds = $this->originalData['teamIds'] ?? [];
        return $this->arraysAreDifferent($this->selectedTeams, $originalTeamIds);
    }

    /**
     * Helper: Check ob Department geändert wurde
     */
    private function departmentHasChanged(): bool
    {
        return $this->department !== ($this->originalData['department_id'] ?? null);
    }

    /**
     * Helper: Check ob Roles geändert wurden
     */
    private function rolesHaveChanged(): bool
    {
        $originalRoleIds = $this->originalData['roleIds'] ?? [];
        return $this->arraysAreDifferent($this->selectedRoles, $originalRoleIds);
    }

    /**
     * Helper: Check ob Phone geändert wurde
     */
    private function phoneHasChanged(): bool
    {
        return $this->phone_1 !== ($this->originalData['phone_1'] ?? '');
    }

    /**
     * Helper: Check ob Model Status geändert wurde
     */
    private function modelStatusHasChanged(): bool
    {
        return $this->model_status !== ($this->originalData['model_status'] ?? '');
    }

    /**
     * Optional: Manuelle Feld-Validierung bei Bedarf
     * Kann in der Blade mit wire:blur="validateField('email')" verwendet werden
     */
    public function validateField(string $fieldName): void
    {
        // Mapping für Array-Felder
        $fieldMapping = [
            'email' => 'emailHasChanged',
            'selectedTeams' => 'teamsHaveChanged',
            'selectedRoles' => 'rolesHaveChanged',
            'department' => 'departmentHasChanged',
        ];

        // Prüfe ob das Feld überhaupt geändert wurde
        if (isset($fieldMapping[$fieldName])) {
            $method = $fieldMapping[$fieldName];
            if (!$this->$method()) {
                return; // Nichts zu validieren
            }
        }

        // Validiere nur dieses eine Feld
        $this->validateOnly($fieldName);
    }
}
