<?php

namespace App\Models\HR;

use App\Enums\Company\CompanyRegistrationType;
use App\Enums\Company\CompanySize;
use App\Enums\Company\CompanyType;
use App\Models\Address\Address;
use App\Models\User;
use App\Traits\HasAddress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use HasAddress;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'owner_id',
        'created_by',
        'industry_id',
        'company_url',
        'company_size',
        'company_type',
        'register_number',
        'email',
        'phone_1',
        'phone_2',
        'registration_type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'company_type' => CompanyType::class,
        'company_size' => CompanySize::class,
        'registration_type' => CompanyRegistrationType::class,
        'is_active' => 'boolean',

    ];


    /** Zeigt zu welcher Branche die Company gehört */
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    /** Relationship to the owner of the company.*/
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** Zusätzliche Validierung damit nur vorhandene Industry gespeichert werden können */
    protected static function booted(): void
    {
        static::saving(function ($company) {
            if (!Industry::where('id', $company->industry_id)->exists()) {
                throw new \InvalidArgumentException('Invalid inadustry_id provided.');
            }
        });
    }

}
