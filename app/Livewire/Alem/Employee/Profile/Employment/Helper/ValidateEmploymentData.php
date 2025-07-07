<?php

namespace App\Livewire\Alem\Employee\Profile\Employment\Helper;

use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use Illuminate\Validation\Rule;

/**
 * Trait für die Validierung in der Employment Data Komponente.
 */
trait ValidateEmploymentData
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
            $rules['joined_at'] = [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:today'
            ];
        }

        // Personal Number - Blockiere nur gefährliche Zeichen
        if ($this->personalNumberHasChanged()) {
            $rules['personal_number'] = [
                'nullable',
                'string',
                'max:12',
                'not_regex:/[<>\"\'%;()&+]/i'  // Blockiere XSS/SQL-Injection gefährliche Zeichen
            ];
        }

        // Probation Enum
        if ($this->probationEnumHasChanged()) {
            $rules['prob_period'] = ['nullable', Rule::enum(Probation::class)];
        }

        // Probation At
        if ($this->probationAtHasChanged()) {
            $rules['probation_at'] = 'nullable|date|date_format:Y-m-d|after_or_equal:joined_at';
        }

        // Notice At
        if ($this->noticeAtHasChanged()) {
            $rules['notice_at'] = 'nullable|date|date_format:Y-m-d|after_or_equal:joined_at';
        }

        // Notice Enum
        if ($this->noticeEnumHasChanged()) {
            $rules['notice_period'] = ['nullable', Rule::enum(NoticePeriod::class)];
        }

        // Leave At
        if ($this->leaveAtHasChanged()) {
            $rules['leave_at'] = 'nullable|date|date_format:Y-m-d|after_or_equal:joined_at';
        }

        return $rules;
    }

    /**
     * Legacy: Alle Validierungsregeln (für Backward Compatibility)
     */
    public function rules(): array
    {
        return [
            'joined_at' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:today'
            ],
            'personal_number' => [
                'nullable',
                'string',
                'max:12',
                'not_regex:/[<>\"\'%;()&+]/i'  // Blockiere XSS/SQL-Injection gefährliche Zeichen
            ],
            'prob_period' => ['nullable', Rule::enum(Probation::class)],
            'probation_at' => 'nullable|date|date_format:Y-m-d|after_or_equal:joined_at',
            'notice_at' => 'nullable|date|date_format:Y-m-d|after_or_equal:joined_at',
            'notice_period' => ['nullable', Rule::enum(NoticePeriod::class)],
            'leave_at' => 'nullable|date|date_format:Y-m-d|after_or_equal:joined_at'
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
            'joined_at.date_format' => __('The joined date must be in format YYYY-MM-DD.'),
            'joined_at.before_or_equal' => __('The joined date cannot be in the future.'),

            // Personal Number
            'personal_number.string' => __('The personal number must be a string.'),
            'personal_number.max' => __('The personal number must not exceed 12 characters.'),
            'personal_number.not_regex' => __('The personal number contains invalid characters.'),

            // Probation
            'prob_period.enum' => __('The selected probation period is invalid.'),
            'probation_at.date' => __('The probation end date must be a valid date.'),
            'probation_at.date_format' => __('The probation end date must be in format YYYY-MM-DD.'),
            'probation_at.after_or_equal' => __('The probation end date must be after or equal to the joined date.'),

            // Notice
            'notice_at.date' => __('The notice date must be a valid date.'),
            'notice_at.date_format' => __('The notice date must be in format YYYY-MM-DD.'),
            'notice_at.after_or_equal' => __('The notice date must be after or equal to the joined date.'),
            'notice_period.enum' => __('The selected notice period is invalid.'),

            // Leave
            'leave_at.date' => __('The leave date must be a valid date.'),
            'leave_at.date_format' => __('The leave date must be in format YYYY-MM-DD.'),
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

        if ($this->probationEnumHasChanged()) {
            $changed['prob_period'] = [
                'old' => $this->originalData['prob_period'] ?? null,
                'new' => $this->prob_period
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
            $changed['notice_period'] = [
                'old' => $this->originalData['notice_period'] ?? null,
                'new' => $this->notice_period
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
        return $this->joined_at !== ($this->originalData['joined_at'] ?? null);
    }

    /**
     * Helper: Check ob Personal Number geändert wurde
     */
    private function personalNumberHasChanged(): bool
    {
        return $this->personal_number !== ($this->originalData['personal_number'] ?? null);
    }

    /**
     * Helper: Check ob Probation Enum geändert wurde
     */
    private function probationEnumHasChanged(): bool
    {
        return $this->prob_period !== ($this->originalData['prob_period'] ?? null);
    }

    /**
     * Helper: Check ob Probation At geändert wurde
     */
    private function probationAtHasChanged(): bool
    {
        return $this->probation_at !== ($this->originalData['probation_at'] ?? null);
    }

    /**
     * Helper: Check ob Notice At geändert wurde
     */
    private function noticeAtHasChanged(): bool
    {
        return $this->notice_at !== ($this->originalData['notice_at'] ?? null);
    }

    /**
     * Helper: Check ob Notice Enum geändert wurde
     */
    private function noticeEnumHasChanged(): bool
    {
        return $this->notice_period !== ($this->originalData['notice_period'] ?? null);
    }

    /**
     * Helper: Check ob Leave At geändert wurde
     */
    private function leaveAtHasChanged(): bool
    {
        return $this->leave_at !== ($this->originalData['leave_at'] ?? null);
    }

    /**
     * Prüft ob irgendwelche Änderungen vorliegen
     */
    public function hasAnyChanges(): bool
    {
        return $this->joinedAtHasChanged() ||
            $this->personalNumberHasChanged() ||
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
            'prob_period' => 'probationEnumHasChanged',
            'probation_at' => 'probationAtHasChanged',
            'notice_at' => 'noticeAtHasChanged',
            'notice_period' => 'noticeEnumHasChanged',
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
