<?php

namespace App\Traits;

use App\Models\HR\Company;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait TraitForUserModel
{

    public function team():BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
