<?php

namespace App\Models\Alem\Employee\Setting;

use App\Models\Alem\Employee\Employee;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profession extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'name',
        'company_id',
        'team_id',
        'created_by',
    ];

    /**
     * Gibt die Mitarbeiter die zu dieser Berufsbezeichnung/ Position zugeorndet sind.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'profession_id');
    }
}
