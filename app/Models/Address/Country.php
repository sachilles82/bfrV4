<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
         /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
        protected $fillable = [
            'name',
            'code',
            'currency',
            'phonecode',
        ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
