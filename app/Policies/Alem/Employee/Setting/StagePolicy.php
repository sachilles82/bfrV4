<?php

namespace App\Policies\Alem\Employee\Setting;

use App\Models\Alem\Employee\Setting\Stage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Stage $stage): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Stage $stage): bool
    {
    }

    public function delete(User $user, Stage $stage): bool
    {
    }

    public function restore(User $user, Stage $stage): bool
    {
    }

    public function forceDelete(User $user, Stage $stage): bool
    {
    }
}
