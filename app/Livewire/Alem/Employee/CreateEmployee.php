<?php

namespace App\Livewire\Alem\Employee;

use App\Enums\User\UserType;
use App\Livewire\Alem\Employee\Helper\ValidateEmployee;
use App\Models\Alem\Employee\Employee;
use App\Models\User;
use App\Models\Alem\Employee\Setting\Profession;
use App\Models\Alem\Employee\Setting\Stage;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateEmployee extends Component
{
    use ValidateEmployee, AuthorizesRequests;

    // Felder für den User
    public $name, $last_name, $email, $password, $gender, $role = 'employee';
    public bool $isActive = false; // Account Status: false = not activated (default), true = active

    // Felder für den Employee
    public $date_hired, $date_fired, $social_number, $personal_number, $profession, $stage;
    public $company_id, $team_id, $created_by, $user_id, $uuid, $probation;

    // Modal-Status
    public bool $showModal = false;

    #[On('professionUpdated')]
    public function getProfessionsProperty()
    {
        return Profession::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    #[On('stageUpdated')]
    public function getStagesProperty()
    {
        return Stage::where('company_id', auth()->user()->company_id)
            ->orderBy('id')
            ->get();
    }

    public function saveEmployee(): void
    {
        $this->authorize('create', Employee::class);
        $this->validate();

        try {
            // Neuen User anlegen und dabei account_status setzen
            $user = User::create([
                'name'           => $this->name,
                'last_name'      => $this->last_name,
                'email'          => $this->email,
                'password'       => Hash::make($this->password),
                'user_type'      => UserType::Employee,
                'company_id'     => auth()->user()->company_id,
                'created_by'     => auth()->id(),
                // Wird als "active" gespeichert, wenn $isActive true ist, ansonsten "inactive"
                'account_status' => $this->isActive ? 'active' : 'inactive',
            ]);
            $user->assignRole($this->role);

            // Employee-Datensatz erstellen und mit dem User verknüpfen
            Employee::create([
                'user_id'         => $user->id,
                'uuid'            => (string) Str::uuid(),
                'date_hired'      => Carbon::parse($this->date_hired),
                'social_number'   => $this->social_number,
                'personal_number' => $this->personal_number,
                'profession'      => $this->profession,
                'stage'           => $this->stage,
                'company_id'      => auth()->user()->company_id,
                'team_id'         => auth()->user()->currentTeam->id,
                'created_by'      => auth()->id(),
            ]);

            $this->resetForm();
            $this->dispatch('employeeCreated');

            Flux::toast(
                text: __('Employee created successfully.'),
                heading: __('Success.'),
                variant: 'success'
            );

            $this->reset();

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

    public function resetForm(): void
    {
        $this->reset([
            'name', 'last_name', 'email', 'password', 'gender', 'role', 'isActive',
            'date_hired', 'social_number', 'personal_number', 'profession', 'stage'
        ]);
        $this->showModal = false;
    }

    public function render(): View
    {
        return view('livewire.alem.employee.create');
    }
}
