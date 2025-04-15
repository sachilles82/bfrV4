<?php

namespace App\Policies\Spatie;

use App\Models\Spatie\Permission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool {}

    public function view(User $user, Permission $permission): bool {}

    public function create(User $user): bool {}

    public function update(User $user, Permission $permission): bool {}

    public function delete(User $user, Permission $permission): bool {}

    public function restore(User $user, Permission $permission): bool {}

    public function forceDelete(User $user, Permission $permission): bool {}
}
