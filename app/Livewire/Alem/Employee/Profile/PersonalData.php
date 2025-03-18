<?php

namespace App\Livewire\Alem\Employee\Profile;

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use App\Models\Alem\Employee\Employee;
use App\Models\Team;
use App\Models\User;
use Flux\Flux;
use Livewire\Component;
use Livewire\Attributes\On;

class PersonalData extends Component
{
    public $employee;
    public $employee_status;
    public $personal_number;
    public $employment_type;
    public $team;
    public $supervisor;
    public $date_hired;
    public $date_fired;
    public $probation;
    public $probation_enum;
    public $notice_period;
    public $notice_period_enum;
    public $user;

    protected $rules = [
        'employee_status' => 'required',
        'personal_number' => 'required|string|max:255',
        'employment_type' => 'required|string|max:255',
        'team' => 'required',
        'supervisor' => 'required|string|max:255',
        'date_hired' => 'required|date',
        'date_fired' => 'nullable|date',
        'probation' => 'required|date',
        'probation_enum' => 'required|string',
        'notice_period' => 'required|string|max:255',
        'notice_period_enum' => 'required|string',
    ];

    protected $messages = [
        'employee_status.required' => 'The employee status is required.',
        'personal_number.required' => 'The personal number is required.',
        'employment_type.required' => 'The employment type is required.',
        'team.required' => 'Please select a team.',
        'supervisor.required' => 'The supervisor is required.',
        'date_hired.required' => 'The hire date is required.',
        'date_fired.after_or_equal' => 'The end date must be after or equal to the hire date.',
        'probation.required' => 'The probation period is required.',
        'probation.date' => 'The probation period must be a valid date.',
        'probation_enum.required' => 'The probation period type is required.',
        'notice_period.required' => 'The notice period is required.',
        'notice_period_enum.required' => 'The notice period type is required.',
    ];

    /**
     * Mount the component with the employee data
     */
    public function mount(User $user = null)
    {
        // If user is passed, load their employee data
        if ($user) {
            // Store the user instance
            $this->user = $user;

            // Load employee relation if not already loaded
            $user->loadMissing('employee');

            // Check if employee exists
            if (!$user->employee) {
                abort(404, __('Employee record not found for this user.'));
            }

            // Store the employee instance
            $this->employee = $user->employee;

            // Load all employee and user data
            $this->loadEmployeeData();
        } else {
            // Handle the case when no user is provided
            abort(404, __('User not found.'));
        }
    }

    public function loadEmployeeData()
    {
        // Employee fields
        $this->employee_status = $this->employee->employee_status ? $this->employee->employee_status->value : null;
        $this->personal_number = $this->employee->personal_number ?? '';
        $this->employment_type = $this->employee->employment_type ?? '';
        $this->team = $this->employee->team_id ?? '';
        $this->supervisor = $this->employee->supervisor ?? '';

        // Handle date fields
        $this->date_fired = $this->formatDateValue($this->employee->date_fired);
        $this->date_hired = $this->formatDateValue($this->employee->date_hired);
        $this->probation = $this->formatDateValue($this->employee->probation);
        $this->notice_period = $this->formatDateValue($this->employee->notice_period);

        // Handle enum fields
        $this->probation_enum = $this->employee->probation_enum ? $this->employee->probation_enum->value : Probation::THREE_MONTHS->value;
        $this->notice_period_enum = $this->employee->notice_period_enum ? $this->employee->notice_period_enum->value : NoticePeriod::THREE_MONTHS->value;
    }

    /**
     * Helper method to format date values safely
     */
    protected function formatDateValue($value)
    {
        // If it's null, return empty string
        if (is_null($value)) {
            return '';
        }

        // If it's already a string, check if it's a valid date string
        if (is_string($value)) {
            // If it looks like a date, return it as is
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                return $value;
            }
            // Otherwise, try to convert it to a date
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return $value; // Return as is if parsing fails
            }
        }

        // If it's a Carbon instance or can be formatted, format it
        try {
            return $value->format('Y-m-d');
        } catch (\Exception $e) {
            return ''; // Return empty string if formatting fails
        }
    }

    /**
     * Helper method to get all EmployeeStatus options for the dropdown
     */
    public function getEmployeeStatusOptionsProperty()
    {
        return collect(EmployeeStatus::cases())->map(function ($status) {
            return [
                'value' => $status->value,
                'label' => $status->label(),
                'dotColor' => $status->dotColor(),
                'icon' => $status->icon()
            ];
        });
    }

    /**
     * Get probation period options for the dropdown
     */
    public function getProbationOptionsProperty()
    {
        return Probation::options();
    }

    /**
     * Get notice period options for the dropdown
     */
    public function getNoticePeriodOptionsProperty()
    {
        return NoticePeriod::options();
    }

    /**
     * Get available teams for the dropdown
     */
    public function getAvailableTeamsProperty()
    {
        return auth()->user()->allTeams();
    }

    /**
     * Update employee and user data
     */
    public function updateEmployee()
    {
        $this->validate();

        // Check if we have an employee to update
        if (!$this->employee) {
            Flux::toast(
                text: __('No employee found to update.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        try {
            // Get the old team ID before updating
            $oldTeamId = $this->employee->team_id;

            // Update employee data
            $this->employee->update([
                'employee_status' => $this->employee_status,
                'personal_number' => $this->personal_number,
                'probation' => $this->probation,
                'probation_enum' => $this->probation_enum,
                'date_fired' => $this->date_fired ?: null,
                'date_hired' => $this->date_hired,
                'employment_type' => $this->employment_type,
                'team_id' => $this->team,
                'supervisor' => $this->supervisor,
                'notice_period' => $this->notice_period,
                'notice_period_enum' => $this->notice_period_enum,
            ]);

            // If team has changed, ensure the user is a member of the new team
            if ($oldTeamId != $this->team) {
                $this->addUserToTeam($this->team);
            }

            Flux::toast(
                text: __('Employee data updated successfully!'),
                heading: __('Success'),
                variant: 'success'
            );

            $this->dispatch('update-table');

        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error updating employee: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Add the employee's user to the specified team if not already a member
     */
    public function addUserToTeam($teamId)
    {
        // Get the team
        $team = Team::find($teamId);

        if (!$team) {
            throw new \Exception(__('Selected team not found.'));
        }

        // Check if user is already a member of this team
        if (!$this->user->belongsToTeam($team)) {
            // Add user to the team as a member
            $this->user->teams()->attach($team, ['role' => 'member']);

            Flux::toast(
                text: __('User added to the selected team.'),
                heading: __('Team Updated'),
                variant: 'info'
            );
        }
    }

    /**
     * Refreshes the component when an update event is received
     */
    #[On('update-table')]
    public function refresh()
    {
        $this->loadEmployeeData();
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.alem.employee.profile.personal-data', [
            'employeeStatusOptions' => $this->employeeStatusOptions,
            'availableTeams' => $this->availableTeams,
            'probationOptions' => $this->probationOptions,
            'noticePeriodOptions' => $this->noticePeriodOptions,
        ]);
    }
}
