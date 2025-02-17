<?php

namespace App\Policies\Address;

use App\Enums\Role\Permission;
use App\Models\Address\Address;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view-any-address');

    }

    public function view(User $user, Address $address): bool
    {
        return $user->can(Permission::CREATE_ADDRESS);
    }

    public function create(User $user): bool
    {
        return $user->can('create-address');
    }

    public function update(User $user, Address $address): bool
    {
        // Falls der User alle Adressen bearbeiten darf
        if ($user->can(Permission::EDIT_ADDRESS)) {
            return true;
        }

        // Falls der User nur seine eigene Adresse bearbeiten darf:
        if ($user->can(Permission::EDIT_OWN_ADDRESS)) {
            // Angenommen, die Adresse gehört über eine Beziehung (z. B. addressable) zum User
            return $address->addressable_id === $user->id;
        }

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
