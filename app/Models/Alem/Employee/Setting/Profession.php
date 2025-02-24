<?php

namespace App\Models\Alem\Employee\Setting;

use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    use HasFactory, BelongsToTeam;

    protected $fillable = [
        'name',
        'company_id',
        'team_id',
        'created_by',
    ];
}
