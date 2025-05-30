<?php

namespace App\Traits;

use App\Models\Team;
use App\Models\User;
use App\Models\Alem\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    use HasScopeFilter;

    protected static function bootBelongsToUser(): void
    {
        static::creating(function (Model $model) {

            $user = Auth::user();

            $model->created_by = $user?->id;

            if ($user) {

                $model->team_id = $user->current_team_id;
                $model->company_id = $user->company_id;
            }
        });
    }

//$model->team_id = Auth::user()->currentTeam->id ?? null;
////            $model->company_id = Auth::user()->company->id ?? null;
////            $model->created_by = Auth::id();

    public function team(): BelongsTo
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
