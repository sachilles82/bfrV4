<?php

namespace App\Policies\HR;

use App\Models\HR\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;

    }

    public function view(User $user, Department $department): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Department $department): bool
    {
        return true;
    }

    public function delete(User $user, Department $department): bool
    {
        return true;
    }

    public function restore(User $user, Department $department): bool
    {
        return true;
    }

    public function forceDelete(User $user, Department $department): bool
    {
        return true;
    }
}
