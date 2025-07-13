<?php

namespace App\Policies\Alem;

use App\Models\Alem\SOS;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SOSPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, SOS $sOS): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, SOS $sOS): bool
    {
    }

    public function delete(User $user, SOS $sOS): bool
    {
    }

    public function restore(User $user, SOS $sOS): bool
    {
    }

    public function forceDelete(User $user, SOS $sOS): bool
    {
    }
}
