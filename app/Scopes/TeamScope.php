<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class TeamScope implements Scope
{
    /**
     * @param Builder $builder
     * @param Model $model
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::check() && Auth::user()->currentTeam) {
            $builder->where('team_id', Auth::user()->currentTeam->id);
        }
    }

}
