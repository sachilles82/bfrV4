<?php

namespace App\Traits;

use App\Models\HR\Company;
use App\Models\Team;
use App\Models\User;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait BelongsToCompany
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */

    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            $model->team_id = Auth::user()->currentTeam->id ?? null;
            $model->company_id = Auth::user()->company->id ?? null;
            $model->created_by = Auth::id();
        });
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
