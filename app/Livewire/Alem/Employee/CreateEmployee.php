<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Employee;
use App\Models\User;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class CreateEmployee extends Component
{
    use ValidateEmployee, AuthorizesRequests;
    // Felder für den User
    public $name, $email, $password, $gender, $role = 'employee';

    // Felder für den Employee
    public $date_hired, $date_fired, $social_number, $personal_number, $profession, $company_id, $team_id, $created_by, $user_id, $uuid, $probation;

    // Modal-Status
    public bool $showModal = false;

    /**
     * Speichert einen neuen Employee inkl. zugehörigem User.
     */
    public function saveEmployee(): void
    {
        try {
            $this->authorize('create', Employee::class);
            $this->validate();

            // Neuen User anlegen
            $user = User::create([
                'name'       => $this->name,
                'email'      => $this->email,
                'password'   => Hash::make($this->password),
                'user_type'  => UserType::Employee,
                'company_id' => auth()->user()->company_id,
                'created_by' => auth()->id(),
            ]);
            $user->assignRole($this->role);

            // Employee-Datensatz erstellen und mit dem User verknüpfen
            Employee::create([
                'user_id'        => $user->id,
                'uuid'           => (string) Str::uuid(),
                'date_hired'     => Carbon::parse($this->date_hired),
                'social_number'  => $this->social_number,
                'personal_number'=> $this->personal_number,
                'profession'     => $this->profession,
                'company_id'     => auth()->user()->company_id,
                'team_id'        => auth()->user()->currentTeam->id,
                'created_by'     => auth()->id(),
            ]);

            $this->resetForm();
            $this->dispatch('employeeCreated');

            Flux::toast(
                text: __('Employee created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->reset();

        } catch (ValidationException $ve) {
            Flux::toast(
                text: __('Validation error. Please check your inputs.'),
                heading: __('Error'),
                variant: 'danger'
            );
        } catch (AuthorizationException $ae) {
            Flux::toast(
                text: __('You are not authorized to create an employee.'),
                heading: __('Unauthorized'),
                variant: 'danger'
            );
        } catch (\Exception $e) {
            Flux::toast(
                text: __('Error creating employee: ') . $e->getMessage(),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Setzt das Formular zurück und schließt das Modal.
     */
    public function resetForm(): void
    {
        $this->reset([
            'name', 'email', 'password', 'gender', 'role',
            'date_hired', 'social_number', 'personal_number', 'profession'
        ]);
        $this->showModal = false;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.create');
    }
}
