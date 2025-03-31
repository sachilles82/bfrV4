<?php

namespace App\Livewire\Alem\Employee\Helper;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

trait Searchable
{
    public $search = '';

    /**
     * Bei Änderung der Sucheingabe die Seite zurücksetzen
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Optimierte Suchfunktion mit datenbankspezifischen Anpassungen
     *
     * @param Builder $query Die aktuelle Query
     * @return Builder Die modifizierte Query
     */
    protected function applySearch(Builder $query): Builder
    {
        if (empty($this->search)) {
            return $query;
        }

        $databaseDriver = Config::get('database.default');

        // MySQL-spezifische Optimierung mit normalisierten Spalten
        if ($databaseDriver === 'mysql') {
            collect(str_getcsv($this->search, ' ', '"'))->filter()->each(function ($term) use ($query) {
                $term = preg_replace('/[^A-Za-z0-9]/', '', $term).'%';
                $query->whereIn('id', function ($query) use ($term) {
                    $query->select('id')
                        ->from(function ($query) use ($term) {
                            $query->select('users.id')
                                ->from('users')
                                ->where('users.name_normalized', 'like', $term)
                                ->orWhere('users.last_name_normalized', 'like', $term)
                                ->union(
                                    $query->newQuery()
                                        ->select('users.id')
                                        ->from('users')
                                        ->where('users.email', 'like', $term)
                                        ->orWhere('users.phone_1', 'like', $term)
                                );
                        }, 'matches');
                });
            });

            return $query;
        }

        // PostgreSQL-spezifische Optimierung mit regexp_replace und ilike
        if ($databaseDriver === 'pgsql') {
            collect(str_getcsv($this->search, ' ', '"'))->filter()->each(function ($term) use ($query) {
                $term = preg_replace('/[^A-Za-z0-9]/', '', $term).'%';
                $query->whereIn('id', function ($query) use ($term) {
                    $query->select('id')
                        ->from(function ($query) use ($term) {
                            $query->select('users.id')
                                ->from('users')
                                ->whereRaw("regexp_replace(users.name, '[^A-Za-z0-9]', '') ilike ?", [$term])
                                ->orWhereRaw("regexp_replace(users.last_name, '[^A-Za-z0-9]', '') ilike ?", [$term])
                                ->union(
                                    $query->newQuery()
                                        ->select('users.id')
                                        ->from('users')
                                        ->whereRaw("users.email ilike ?", [$term])
                                        ->orWhereRaw("users.phone_1 ilike ?", [$term])
                                );
                        }, 'matches');
                });
            });

            return $query;
        }

        // SQLite-Fallback (einfachere Implementierung für die Entwicklung)
        if ($databaseDriver === 'sqlite') {
            // Parse search terms, allowing for quoted phrases
            $terms = collect(str_getcsv($this->search, ' ', '"'))->filter();

            return $query->where(function($query) use ($terms) {
                foreach ($terms as $term) {
                    $likeTerm = '%' . $term . '%';  // Für SQLite verwenden wir beide Wildcards für bessere Resultate

                    $query->where(function($subQuery) use ($likeTerm) {
                        $subQuery->where('users.name', 'like', $likeTerm)
                            ->orWhere('users.last_name', 'like', $likeTerm)
                            ->orWhere('users.email', 'like', $likeTerm)
                            ->orWhere('users.phone_1', 'like', $likeTerm);
                    });
                }
            });
        }

        // Generischer Fallback für andere Datenbanken
        $terms = collect(str_getcsv($this->search, ' ', '"'))->filter();

        return $query->where(function($query) use ($terms) {
            foreach ($terms as $term) {
                $likeTerm = $term . '%';  // Trailing wildcard für bessere Index-Nutzung

                $query->where(function($subQuery) use ($likeTerm) {
                    $subQuery->where('users.name', 'like', $likeTerm)
                        ->orWhere('users.last_name', 'like', $likeTerm)
                        ->orWhere('users.email', 'like', $likeTerm)
                        ->orWhere('users.phone_1', 'like', $likeTerm);
                });
            }
        });
    }
}
