<?php

namespace App\Policies\Address;

use App\Models\Address\Address;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Address $address): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Address $address): bool
    {
        return false;
    }

    public function delete(User $user, Address $address): bool
    {
    }

    public function restore(User $user, Address $address): bool
    {
    }

    public function forceDelete(User $user, Address $address): bool
    {
    }
}
