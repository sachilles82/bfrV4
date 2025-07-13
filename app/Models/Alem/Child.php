<?php

namespace App\Models\Alem;

use App\Enums\User\Gender;
use App\Models\User;
use App\Traits\Cache\AdvancedCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Child extends Model
{
    use AdvancedCache;

    /**
     * Cache-Konfiguration
     */
    protected int $cacheDuration = 43200; // 12 Stunden
    protected string $cachePrefix = 'children';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'gender',
        'birthdate',
        'ahv_number',
        'valid_until',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'birthdate' => 'date',
        'valid_until' => 'date',
        'gender' => Gender::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ein Kind gehört zu einem User (Parent)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Berechnet das Alter des Kindes
     */
    public function getAgeAttribute(): int
    {
        return $this->birthdate ? $this->birthdate->age : 0;
    }

    /**
     * Prüft ob das Kind noch kindergeldberechtigt ist
     */
    public function getIsValidAttribute(): bool
    {
        if (!$this->valid_until) {
            return true;
        }

        return $this->valid_until->isFuture();
    }

    /**
     * Cache-Kontexte für Auto-Flush
     */
    protected function getAutoFlushContexts(): array
    {
        return ['user'];
    }
}
