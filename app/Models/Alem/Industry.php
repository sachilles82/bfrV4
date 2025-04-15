<?php

namespace App\Models\Alem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

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

    protected static function booted()
    {
        // Bei jedem Speichern (Erstellen oder Aktualisieren) den Cache löschen
        static::saved(function () {
            Cache::forget('industries');
        });

        // Bei Löschung ebenfalls
        static::deleted(function () {
            Cache::forget('industries');
        });
    }
}
