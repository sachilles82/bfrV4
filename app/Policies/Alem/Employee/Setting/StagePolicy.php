<?php

namespace App\Policies\Alem\Employee\Setting;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool {}

    public function view(User $user, \App\Models\Alem\QuickCrud\Stage $stage): bool {}

    public function create(User $user): bool {}

    public function update(User $user, \App\Models\Alem\QuickCrud\Stage $stage): bool {}

    public function delete(User $user, \App\Models\Alem\QuickCrud\Stage $stage): bool {}

    public function restore(User $user, \App\Models\Alem\QuickCrud\Stage $stage): bool {}

    public function forceDelete(User $user, \App\Models\Alem\QuickCrud\Stage $stage): bool {}
}
