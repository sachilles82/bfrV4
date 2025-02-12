<?php

namespace App\Policies\Address;

use App\Models\Address\State;
use App\Models\User;

class StatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, State $state): bool
    {
//       return $user->id === $state->created_by:
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-state');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, State $state): bool
    {
        if ($user->can('edit-all-state')) {
            return true;
        }

        if ($user->can('edit-state')) {
            return $user->id == $state->created_by;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, State $state): bool
    {
        //
    }
}
