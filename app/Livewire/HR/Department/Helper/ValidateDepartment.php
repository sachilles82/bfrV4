<?php

namespace App\Livewire\HR\Department\Helper;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

trait ValidateDepartment
{
    /** Definiert die Validierungsregeln.*/
    public function rules(): array
    {
        $teamId = Auth::user()->currentTeam->id;

        return [
            'name' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9 ]+$/',
                'max:25',
                'min:2',
                Rule::unique('departments', 'name')
                    ->where('team_id', $teamId) // Sicherstellen, dass es nur innerhalb des gleichen Teams einzigartig ist
                    ->ignore($this->departmentId),
            ],
        ];
    }

    /** Definiert benutzerdefinierte Fehlermeldungen für die Validierungsregeln.*/
    public function messages(): array
    {
        return [
            'name.required' => __('Please add a name.'),
            'name.string' => __('The entry must be a string.'),
            'name.min' => __('The entry must be at least 2 characters long.'),
            'name.regex' => __('The entry may only contain letters, numbers, and spaces.'),
            'name.unique' => __('The entry exists already!'),
        ];
    }

}
