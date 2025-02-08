<?php

namespace App\Models\Address;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class City extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'state_id',
        'team_id',
        'created_by',
    ];

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (! $model->created_by) {
                $model->created_by = auth()->id();
            }

            if ($user = auth()->user()) {
                $model->team_id ??= $user->currentTeam?->id;
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
