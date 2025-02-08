<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CompanyScope implements Scope
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
        if (Auth::check() && Auth::user()->company) {
            $builder->where('company_id', Auth::user()->company->id);
        }
    }

}
