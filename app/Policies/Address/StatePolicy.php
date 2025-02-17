<?php

namespace App\Policies\Address;

use App\Enums\Role\Permission;
use App\Models\Address\State;
use App\Models\User;

class StatePolicy
{

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
//        return $user->can(Permission::CREATE_STATE);
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, State $state): bool
    {
//        if ($user->can(Permission::EDIT_ALL_STATE)) {
//            return true;
//        }
//
//        if ($user->can(Permission::EDIT_OWN_STATE)) {
//            return $user->id == $state->created_by;
//        }
//
//        return false;
        //nur zu Testzwecken
                return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, State $state): bool
    {
//        if ($user->can(Permission::DELETE_ALL_STATE)) {
//            return true;
//        }
//
//        if ($user->can(Permission::DELETE_OWN_STATE)) {
//            return $user->id == $state->created_by;
//        }
//
//        return false;

        return true;
    }
}
