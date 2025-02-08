<?php

namespace App\Livewire\Spatie\Role\Helper;

trait ValidateRole
{
    /**
     * Definiert die Validierungsregeln.
     */
    protected $rules = [
        'name' => ['required', 'string', 'min:2', 'max:20', 'regex:/^[\pL\pN\s]*$/u'],
        'description' => ['nullable', 'string', 'min:2', 'max:60', 'regex:/^[\pL\pN\s]*$/u'],
        'access' => ['required', 'string', 'in:employee_panel,partner_panel'],
    ];

    /**
     * Definiert benutzerdefinierte Fehlermeldungen für die Validierungsregeln.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('Please add a name.'),
            'name.string' => __('The entry must be a string.'),
            'name.min' => __('The entry must be at least 2 characters long.'),
            'name.regex' => __('The entry may only contain letters, numbers, and spaces.'),
            'description.string' => __('The entry must be a string.'),
            'description.min' => __('The entry must be at least 2 characters long.'),
            'description.regex' => __('The entry may only contain letters, numbers, and spaces.'),
            'access.required' => __('Please select an access.'),
            'access.string' => __('The entry must be a string.'),
            'access.in' => __('This panel does not exist.'),
        ];
    }
}
