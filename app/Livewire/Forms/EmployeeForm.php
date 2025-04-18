<?php

namespace App\Livewire\Forms;

use App\Models\User;
use App\Models\Team;
use App\Models\Alem\Employee;
use Illuminate\Support\Facades\DB;
use Barryvdh\Debugbar\Facades\Debugbar;
use Flux\Flux;
use Livewire\Attributes\Rule;
use Livewire\Form;

class EmployeeForm extends Form
{
    public ?User $user = null;
    public bool $editMode = false;
    
    // User Daten
    #[Rule('required|string|min:2')]
    public $name;
    
    #[Rule('required|string|min:2')]
    public $last_name;
    
    #[Rule('required|email')]
    public $email;
    
    public $gender;
    
    #[Rule('required')]
    public $model_status;
    
    public $joined_at;
    
    #[Rule('nullable|exists:departments,id')]
    public $department;
    
    #[Rule('required|array|min:1')]
    public $selectedTeams = [];
    
    #[Rule('required|array|min:1')]
    public $selectedRoles = [];
    
    // Mitarbeiterdaten
    #[Rule('required')]
    public $employee_status;
    
    #[Rule('required|exists:professions,id')]
    public $profession;
    
    #[Rule('required|exists:stages,id')]
    public $stage;
    
    #[Rule('nullable|exists:users,id')]
    public $supervisor;
    
    /**
     * Employee-Daten aus dem Benutzer setzen
     */
    public function setUser(User $user): void
    {
        $this->reset();
        
        $this->user = $user;
        $this->editMode = true;
        
        // Benutzerdaten
        $this->name = $user->name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->gender = $user->gender;
        $this->model_status = $user->model_status;
        $this->joined_at = $user->joined_at;
        $this->department = $user->department_id;
        
        // Teams und Rollen
        $this->selectedTeams = $user->teams->pluck('id')->toArray();
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
        
        // Mitarbeiterdaten
        if ($user->employee) {
            $this->employee_status = $user->employee->employee_status;
            $this->profession = $user->employee->profession_id;
            $this->stage = $user->employee->stage_id;
            $this->supervisor = $user->employee->supervisor_id;
        }
    }
    
    /**
     * Mitarbeiter aktualisieren
     */
    public function updateEmployee(): void
    {
        if (!$this->editMode || !$this->user) {
            Flux::toast(
                text: __('Fehler: Kein Mitarbeiter zum Aktualisieren ausgewählt.'),
                heading: __('Fehler'),
                variant: 'danger'
            );
            return;
        }
        
        try {
            DB::beginTransaction();
            
            // Benutzerdaten aktualisieren
            $this->user->update([
                'name' => $this->name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'gender' => $this->gender,
                'model_status' => $this->model_status,
                'joined_at' => $this->joined_at,
                'department_id' => $this->department,
            ]);
            
            // Teams und Rollen synchronisieren
            $this->user->teams()->sync($this->selectedTeams);
            $this->user->roles()->sync($this->selectedRoles);
            
            // Mitarbeiterdaten aktualisieren oder erstellen
            if ($this->user->employee) {
                $this->user->employee->update([
                    'employee_status' => $this->employee_status,
                    'profession_id' => $this->profession,
                    'stage_id' => $this->stage,
                    'supervisor_id' => $this->supervisor,
                ]);
            } else {
                Employee::create([
                    'user_id' => $this->user->id,
                    'employee_status' => $this->employee_status,
                    'profession_id' => $this->profession,
                    'stage_id' => $this->stage,
                    'supervisor_id' => $this->supervisor,
                ]);
            }
            
            DB::commit();
            
            Flux::toast(
                text: __('Mitarbeiter erfolgreich aktualisiert.'),
                heading: __('Erfolg'),
                variant: 'success'
            );
            
            // Form zurücksetzen
            $this->reset();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Debugbar::error('Fehler beim Aktualisieren des Mitarbeiters: ' . $e->getMessage());
            
            Flux::toast(
                text: __('Fehler beim Aktualisieren des Mitarbeiters: ') . $e->getMessage(),
                heading: __('Fehler'),
                variant: 'danger'
            );
        }
    }
}
