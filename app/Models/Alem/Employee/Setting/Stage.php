<?php

namespace App\Models\Alem\Employee\Setting;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'name',
        'company_id',
        'team_id',
        'created_by',
    ];
}
