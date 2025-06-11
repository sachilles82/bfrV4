<?php

namespace App\Livewire\Alem\Employee\Helper;

use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Trait für die Fehlerbehandlung in den Employee Komponenten.
 */
trait HandleCatchError
{
    private function handleSavingError(\Throwable $e): void
    {
        Log::error("Fehler beim Erstellen des Mitarbeiters: {$e->getMessage()}", [
            'exception' => $e,
            'acting_user_id' => $this->authUserId ?? auth()->id(),
            'formData' => $this->only([
                'gender',
                'name',
                'last_name',
                'email',
                'model_status',
                'joined_at',
                'department',
                'selectedTeams',
                'selectedRoles',
                'employee_status',
                'profession',
                'stage',
                'supervisor',
                'invitation'
            ])
        ]);

        Flux::toast(
            text: __('An error occurred while saving the employee.'),
            heading: __('Error.'),
            variant: 'danger'
        );
    }

    private function handleEditingError(\Throwable $e): void
    {
        DB::rollBack();

        Log::error("Fehler beim Erstellen des Mitarbeiters: {$e->getMessage()}", [
            'exception' => $e,
            'acting_user_id' => $this->authUserId ?? auth()->id(),
            'formData' => $this->only([
                'gender',
                'name',
                'last_name',
                'email',
                'model_status',
                'joined_at',
                'department',
                'selectedTeams',
                'selectedRoles',
                'employee_status',
                'profession',
                'stage',
                'supervisor'
            ])
        ]);

        Flux::toast(
            text: __('An error occurred while saving the employee.'),
            heading: __('Error.'),
            variant: 'danger'
        );
    }

    private function handleLoadingError(\Throwable $e): void
    {
        Log::error("Fehler beim Laden der Relationen: " . $e->getMessage());

        Flux::toast(
            text: __('An error occurred while loading the Relation Data.'),
            heading: __('Error.'),
            variant: 'danger'
        );
    }





}
