<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Scope;

trait HasScopeFilter
{
    protected static function bootAppliesConfigurableGlobalScope(): void
    {
        if (
            isset(static::$scopeClass) &&
            is_subclass_of(static::$scopeClass, Scope::class)
        ) {
            static::addGlobalScope(new static::$scopeClass());
        }
    }
}
