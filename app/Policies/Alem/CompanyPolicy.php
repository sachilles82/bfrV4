<?php

namespace App\Policies\Alem;

use App\Models\Alem\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Company $company): bool
    {
        // Nur der Besitzer darf es sehen (Beispiel):
        return $user->id === $company->owner_id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, \App\Models\Alem\Company $company): bool
    {
        // Nur der Besitzer darf es sehen (Beispiel):
        return $user->id === $company->owner_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company): bool
    {
        return $user->id === $company->owner_id;
    }

}
