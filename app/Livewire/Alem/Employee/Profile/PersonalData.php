<?php

namespace App\Livewire\Alem\Employee\Profile;

use Livewire\Component;

class PersonalData extends Component
{
    public $employee_status;
    public $employment_type;
    public $supervisor;
    public $personal_number;
    public $date_hired;
    public $date_fired;
    public $notice_period;
    public $probation;

    public function mount()
    {
        // Dummy-Daten für das Formular
        $this->employee_status = 'probation';
        $this->employment_type = '80% an 4 Tagen pro Woche (seit 01.01.2025)';
        $this->supervisor = 'Fabienne Flückiger (Head of Finance)';
        $this->personal_number = '10567';
        $this->date_hired = '2025-01-01';
        $this->date_fired = ''; // optional
        $this->notice_period = '3 Monate';
        $this->probation = '3 Monate (bis 31.03.2025)';
    }

    public function updateEmployee()
    {
        // Hier würdest du die Validierung und Speicherung vornehmen
        session()->flash('message', 'Daten aktualisiert!');
    }

    public function render()
    {
        return view('livewire.alem.employee.profile.personal-data');
    }
}
