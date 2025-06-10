<?php

namespace App\Livewire\Alem\Employee\Helper;

use Flux\Flux;
use Illuminate\Support\Facades\Log;

/**
 * Trait für die Fehlerbehandlung in der CreateEmployee-Komponente.
 */
trait HandleCatchError
{
    private function handleError(\Throwable $e): void
    {
        Log::error("Fehler beim Erstellen des Mitarbeiters: {$e->getMessage()}", [
            'exception' => $e,
            'acting_user_id' => $this->authUserId,
            'formData' => $this->only
            ([
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
                'invitations'
            ])
        ]);

        Flux::toast(
            text: __('An error occurred while saving the employee.'),
            heading: __('Error.'),
            variant: 'danger'
        );
    }

}
