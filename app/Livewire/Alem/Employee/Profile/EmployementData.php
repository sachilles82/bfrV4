<?php

namespace App\Livewire\Alem\Employee\Profile;

use Livewire\Component;

class EmployementData extends Component
{
    public $ahv_number;
    public $birthdate;
    public $nationality;
    public $hometown;
    public $religion;
    public $civil_status;
    public $residence_permit;
    public $iban;

    public function mount()
    {
        // Dummy-Daten initialisieren
        $this->ahv_number       = '756.1234.5678.97';
        $this->birthdate        = '1987-06-10'; // ISO-Format für Date-Input
        $this->nationality      = 'Schweizer';
        $this->hometown         = 'Quarten SG';
        $this->religion         = 'Keine Angabe';
        $this->civil_status     = 'Ledig';
        $this->residence_permit = 'B';
        $this->iban             = 'CH93 0076 2011 6238 5295 27';
    }

    public function updateEmployee()
    {
        // Hier würdest du Validierung und Speicherung umsetzen
        session()->flash('message', 'Employment data updated!');
    }

    public function render()
    {
        return view('livewire.alem.employee.profile.employement-data');
    }
}
