<?php

namespace App\Policies\Address;

use App\Enums\Role\Permission;
use App\Models\Address\Address;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class AddressablePolicy
{
    use HandlesAuthorization;

    /**
     * Prüft, ob der Benutzer das addressable Objekt (also dessen Adresse) aktualisieren darf.
     */
    public function update(User $user, Model $addressable): bool
    {
        // Falls der User die allgemeine Berechtigung zum Bearbeiten hat:
        if ($user->can(Permission::EDIT_ADDRESS)) {
            return true;
        }

        // Falls der User nur seine eigene Adresse bearbeiten darf:
        if ($user->can(Permission::EDIT_OWN_ADDRESS)) {
            return true;
        }

        return false;
    }

    /**
     * Optional: Jeder darf die Adresse ansehen.
     */
    public function view(User $user, Model $addressable): bool
    {
        return true;
    }
}
