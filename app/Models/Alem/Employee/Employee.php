<?php

namespace App\Models\Alem\Employee;

use App\Models\User;
use App\Traits\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use BelongsToTeam;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'date_hired',
        'date_fired',
        'probation',
        'social_number',
        'personal_number',
        'profession',
        'company_id',
        'team_id',
        'created_by',
    ];

    protected $casts = [
        'date_hired' => 'date',
        'date_fired' => 'date',
        'probation' => 'date',
    ];

    /** Employee gehört zu einem User */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
