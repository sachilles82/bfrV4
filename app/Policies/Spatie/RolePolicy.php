<?php

namespace App\Policies\Spatie;

use App\Enums\Role\Permission;
use App\Models\Spatie\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Role $role): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can(Permission::CREATE_ROLE);
    }

    public function update(User $user, Role $role): bool
    {
        //        if ($user->can(Permission::UPDATE_ROLE_PERMISSIONS)) {
        //            return true;
        //        }

        if ($user->can(Permission::UPDATE_OWN_ROLE_PERMISSIONS)) {
            return $user->id == $role->created_by;
        }
    }

    /**
     * Delete a specific Role.
     * The user can delete the role only if they are the creator.
     */
    public function delete(User $user, Role $role): bool
    {
        //        if ($user->can(Permission::DELETE_ROLE)) {
        //            return true;
        //        }

        if ($user->can(Permission::DELETE_OWN_ROLE)) {
            return $user->id == $role->created_by;
        }

    }

    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}
