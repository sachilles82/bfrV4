<?php

namespace App\Policies\Alem\Employee\Setting;

use App\Models\Alem\Employee\Setting\Profession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfessionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Profession $profession): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Profession $profession): bool
    {
    }

    public function delete(User $user, Profession $profession): bool
    {
    }

    public function restore(User $user, Profession $profession): bool
    {
    }

    public function forceDelete(User $user, Profession $profession): bool
    {
    }
}
