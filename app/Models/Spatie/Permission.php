<?php

namespace App\Models\Spatie;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'guard_name',
        'description',
        'model',
    ];
}
