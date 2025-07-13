<?php

namespace App\Models\Alem;

use App\Enums\User\Gender;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SOS extends Model
{
    protected $table = 'sos';

    protected $fillable = [
        'user_id',
        'gender',
        'name',
        'related',
        'phone',
        'email',
    ];

    /**
     * Type-Casting für Attribute
     */
    protected $casts = [
        'gender' => Gender::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Gibt den SOS Kontakt zurück, der dem Mitarbeiter zugeordnet ist.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
