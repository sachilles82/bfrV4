<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Performance: Einmaliger Auth-Check
        $user = Auth::user();

        if (!$user) {
            // Sicherheit: Keine Daten für nicht-authentifizierte User
            $builder->whereRaw('0 = 1');
            return;
        }

        // Table-Qualifizierung für JOIN-Sicherheit
        $table = $model->getTable();

        if ($user->company_id) {
            $builder->where("{$table}.company_id", $user->company_id);
        } else {
            // Fallback: Keine Daten wenn keine Company
            $builder->whereRaw('0 = 1');
        }
    }
}
