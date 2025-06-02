<?php

namespace App\Traits\Model;

use App\Models\Alem\Company;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Trait für automatische Verwaltung von Ownership-Feldern
 *
 * Füllt automatisch created_by, team_id und company_id beim Erstellen
 * Stellt die entsprechenden Relationen bereit
 */
trait ManagesContextAndOwnership
{
    /**
     * Boot-Methode für automatische Feld-Befüllung
     *
     * @return void
     */
    protected static function bootManagesContextAndOwnership(): void
    {
        static::creating(function (Model $model) {
            $user = Auth::user();

            if ($user) {
                $model->created_by = $user->id;
                $model->team_id = $user->current_team_id;
                $model->company_id = $user->company_id;
            }
        });
    }

    /**
     * Relation zum Team
     *
     * @return BelongsTo
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relation zur Company
     *
     * @return BelongsTo
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation zum Ersteller
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
