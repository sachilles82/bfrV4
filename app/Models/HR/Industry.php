<?php

namespace App\Models\HR;

use App\Models\HR\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    use HasFactory;

    /**
         * The attributes that are mass assignable.
         *
         * @var array<int, string>
         */
        protected $fillable = [
            'name',
        ];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}
