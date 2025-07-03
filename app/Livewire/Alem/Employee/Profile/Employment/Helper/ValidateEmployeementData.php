<?php

namespace App\Livewire\Alem\Employee\Profile\Employment\Helper;

use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use Illuminate\Validation\Rule;

/**
 * Trait für die Fehlerbehandlung in der PersonalData Komponente.
 */
trait ValidateEmployeementData
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

        // Joined At
        if ($this->joinedAtHasChanged()) {
            $rules['joined_at'] = 'required|date|before_or_equal:today';
        }

        // Personal Number
        if ($this->personalNumberHasChanged()) {
            $rules['personal_number'] = 'nullable|string|max:50';
        }

        // Employment Type
        if ($this->employmentTypeHasChanged()) {
            $rules['employment_type'] = 'required|string|max:100';
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

        // Probation Enum
        if ($this->probationEnumHasChanged()) {
            $rules['probation_enum'] = ['required', Rule::enum(Probation::class)];
        }

        // Probation At
        if ($this->probationAtHasChanged()) {
            $rules['probation_at'] = 'nullable|date|after_or_equal:joined_at';
        }

        // Notice At
        if ($this->noticeAtHasChanged()) {
            $rules['notice_at'] = 'nullable|date';
        }

        // Notice Enum
        if ($this->noticeEnumHasChanged()) {
            $rules['notice_enum'] = ['nullable', Rule::enum(NoticePeriod::class)];
        }

        // Leave At
        if ($this->leaveAtHasChanged()) {
            $rules['leave_at'] = 'nullable|date|after_or_equal:joined_at';
        }

        return $rules;
    }

    /**
     * Legacy: Alle Validierungsregeln (für Backward Compatibility)
     */
    public function rules(): array
    {
        return [
            'joined_at' => 'required|date|before_or_equal:today',
            'personal_number' => 'nullable|string|max:50',
            'employment_type' => 'required|string|max:100',
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
            'probation_enum' => ['required', Rule::enum(Probation::class)],
            'probation_at' => 'nullable|date|after_or_equal:joined_at',
            'notice_at' => 'nullable|date',
            'notice_enum' => ['nullable', Rule::enum(NoticePeriod::class)],
            'leave_at' => 'nullable|date|after_or_equal:joined_at'
        ];
    }

    /**
     * Validierungs-Nachrichten
     */
    public function messages(): array
    {
        return [
            // Joined At
            'joined_at.required' => __('The joined date is required.'),
            'joined_at.date' => __('The joined date must be a valid date.'),
            'joined_at.before_or_equal' => __('The joined date cannot be in the future.'),

            // Personal Number
            'personal_number.string' => __('The personal number must be a string.'),
            'personal_number.max' => __('The personal number must not exceed 50 characters.'),

            // Employment Type
            'employment_type.required' => __('The employment type is required.'),
            'employment_type.string' => __('The employment type must be a string.'),
            'employment_type.max' => __('The employment type must not exceed 100 characters.'),

            // Profession
            'profession.required' => __('The profession is required.'),
            'profession.integer' => __('The profession must be a number.'),
            'profession.in' => __('The selected profession is invalid.'),

            // Stage
            'stage.required' => __('The stage is required.'),
            'stage.integer' => __('The stage must be a number.'),
            'stage.in' => __('The selected stage is invalid.'),

            // Probation
            'probation_enum.required' => __('The probation period is required.'),
            'probation_enum.enum' => __('The selected probation period is invalid.'),
            'probation_at.date' => __('The probation end date must be a valid date.'),
            'probation_at.after_or_equal' => __('The probation end date must be after or equal to the joined date.'),

            // Notice
            'notice_at.date' => __('The notice date must be a valid date.'),
            'notice_enum.enum' => __('The selected notice period is invalid.'),

            // Leave
            'leave_at.date' => __('The leave date must be a valid date.'),
            'leave_at.after_or_equal' => __('The leave date must be after or equal to the joined date.'),
        ];
    }

    /**
     * Gibt alle geänderten Felder mit ihren Änderungen zurück
     */
    public function getChangedFields(): array
    {
        $changed = [];

        if ($this->joinedAtHasChanged()) {
            $changed['joined_at'] = [
                'old' => $this->originalData['joined_at'] ?? null,
                'new' => $this->joined_at
            ];
        }

        if ($this->personalNumberHasChanged()) {
            $changed['personal_number'] = [
                'old' => $this->originalData['personal_number'] ?? null,
                'new' => $this->personal_number
            ];
        }

        if ($this->employmentTypeHasChanged()) {
            $changed['employment_type'] = [
                'old' => $this->originalData['employment_type'] ?? null,
                'new' => $this->employment_type
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

        if ($this->probationEnumHasChanged()) {
            $changed['probation_enum'] = [
                'old' => $this->originalData['probation_enum'] ?? null,
                'new' => $this->probation_enum
            ];
        }

        if ($this->probationAtHasChanged()) {
            $changed['probation_at'] = [
                'old' => $this->originalData['probation_at'] ?? null,
                'new' => $this->probation_at
            ];
        }

        if ($this->noticeAtHasChanged()) {
            $changed['notice_at'] = [
                'old' => $this->originalData['notice_at'] ?? null,
                'new' => $this->notice_at
            ];
        }

        if ($this->noticeEnumHasChanged()) {
            $changed['notice_enum'] = [
                'old' => $this->originalData['notice_enum'] ?? null,
                'new' => $this->notice_enum
            ];
        }

        if ($this->leaveAtHasChanged()) {
            $changed['leave_at'] = [
                'old' => $this->originalData['leave_at'] ?? null,
                'new' => $this->leave_at
            ];
        }

        return $changed;
    }

    /**
     * Helper: Check ob Joined At geändert wurde
     */
    private function joinedAtHasChanged(): bool
    {
        return $this->joined_at !== ($this->originalData['joined_at'] ?? '');
    }

    /**
     * Helper: Check ob Personal Number geändert wurde
     */
    private function personalNumberHasChanged(): bool
    {
        return $this->personal_number !== ($this->originalData['personal_number'] ?? '');
    }

    /**
     * Helper: Check ob Employment Type geändert wurde
     */
    private function employmentTypeHasChanged(): bool
    {
        return $this->employment_type !== ($this->originalData['employment_type'] ?? '');
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
     * Helper: Check ob Probation Enum geändert wurde
     */
    private function probationEnumHasChanged(): bool
    {
        return $this->probation_enum !== ($this->originalData['probation_enum'] ?? null);
    }

    /**
     * Helper: Check ob Probation At geändert wurde
     */
    private function probationAtHasChanged(): bool
    {
        return $this->probation_at !== ($this->originalData['probation_at'] ?? '');
    }

    /**
     * Helper: Check ob Notice At geändert wurde
     */
    private function noticeAtHasChanged(): bool
    {
        return $this->notice_at !== ($this->originalData['notice_at'] ?? '');
    }

    /**
     * Helper: Check ob Notice Enum geändert wurde
     */
    private function noticeEnumHasChanged(): bool
    {
        return $this->notice_enum !== ($this->originalData['notice_enum'] ?? null);
    }

    /**
     * Helper: Check ob Leave At geändert wurde
     */
    private function leaveAtHasChanged(): bool
    {
        return $this->leave_at !== ($this->originalData['leave_at'] ?? '');
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->joinedAtHasChanged() ||
            $this->personalNumberHasChanged() ||
            $this->employmentTypeHasChanged() ||
            $this->professionHasChanged() ||
            $this->stageHasChanged() ||
            $this->probationEnumHasChanged() ||
            $this->probationAtHasChanged() ||
            $this->noticeAtHasChanged() ||
            $this->noticeEnumHasChanged() ||
            $this->leaveAtHasChanged();
    }

    /**
     * Validiere einzelnes Feld on-the-fly (z.B. wire:blur)
     */
    public function validateField(string $fieldName): void
    {
        $fieldMapping = [
            'joined_at' => 'joinedAtHasChanged',
            'personal_number' => 'personalNumberHasChanged',
            'employment_type' => 'employmentTypeHasChanged',
            'profession' => 'professionHasChanged',
            'stage' => 'stageHasChanged',
            'probation_enum' => 'probationEnumHasChanged',
            'probation_at' => 'probationAtHasChanged',
            'notice_at' => 'noticeAtHasChanged',
            'notice_enum' => 'noticeEnumHasChanged',
            'leave_at' => 'leaveAtHasChanged',
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
}
