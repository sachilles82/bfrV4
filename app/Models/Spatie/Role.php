<?php

namespace App\Models\Spatie;

use App\Enums\Role\RoleHasAccessTo;
use App\Enums\Role\RoleVisibility;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'access',
        'visible',
        'is_manager',
        'company_id',
        'team_id',
        'created_by',
    ];

    protected $casts = [
        'access' => RoleHasAccessTo::class,
        'visible' => RoleVisibility::class,
        'is_manager' => 'boolean',
    ];

    protected $attributes = [
        'guard_name' => 'web',
    ];

    protected static function booted(): void
    {
        static::creating(function ($role) {
            $role->guard_name = 'web';

            if (! $role->created_by) {
                $role->created_by = auth()->id();
            }

            $user = auth()->user();

            if ($user) {
                if (! $role->company_id) {
                    $role->company_id = $user->company_id;
                }
                if (! $role->team_id) {
                    $role->team_id = $user->currentTeam?->id;
                }
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
