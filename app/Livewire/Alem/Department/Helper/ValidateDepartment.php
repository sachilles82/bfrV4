<?php

namespace App\Livewire\Alem\Department\Helper;

use App\Enums\Model\ModelStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait ValidateDepartment
{

    public function rules(): array
    {
        $teamId = Auth::user()->currentTeam->id;

        return [
            'name' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9äöüÄÖÜß\s]+$/',
                'not_regex:/<|>/',
                'max:25',
                'min:2',
                Rule::unique('departments', 'name')
                    ->where('team_id', $teamId)
                    ->ignore($this->departmentId),
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
                'regex:/^[a-zA-Z0-9äöüÄÖÜß\s\.,;:!?\-_()\/\'\"]+$/',
                'not_regex:/<|>/',
            ],
            'model_status' => [
                'required',
                'string',
                new Enum(ModelStatus::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // Name-Validierung Fehlermeldungen
            'name.required' => __('Bitte geben Sie einen Abteilungsnamen ein.'),
            'name.string' => __('Der Abteilungsname muss eine Zeichenkette sein.'),
            'name.min' => __('Der Abteilungsname muss mindestens 2 Zeichen lang sein.'),
            'name.max' => __('Der Abteilungsname darf nicht länger als 25 Zeichen sein.'),
            'name.regex' => __('Der Abteilungsname darf nur Buchstaben, Zahlen, Umlaute und Leerzeichen enthalten.'),
            'name.not_regex' => __('Der Abteilungsname darf keine spitzen Klammern (< oder >) enthalten.'),
            'name.unique' => __('Eine Abteilung mit diesem Namen existiert bereits in Ihrem Team.'),

            // Beschreibung-Validierung Fehlermeldungen
            'description.string' => __('Die Beschreibung muss eine Zeichenkette sein.'),
            'description.max' => __('Die Beschreibung darf nicht länger als 1000 Zeichen sein.'),
            'description.regex' => __('Die Beschreibung darf nur Buchstaben, Zahlen, Umlaute, Leerzeichen und grundlegende Satzzeichen enthalten.'),
            'description.not_regex' => __('Die Beschreibung darf keine spitzen Klammern (< oder >) enthalten.'),

            // Status-Validierung Fehlermeldungen
            'model_status.required' => __('Bitte wählen Sie einen Status aus.'),
            'model_status.string' => __('Der Status muss eine gültige Option sein.'),
            'model_status.enum' => __('Der Status muss einer der folgenden Werte sein: Aktiv, Archiviert oder Im Papierkorb.'),
        ];
    }
}
