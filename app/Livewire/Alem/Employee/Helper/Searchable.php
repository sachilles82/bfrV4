<?php

namespace App\Livewire\Alem\Employee\Helper;

// In app/Livewire/Alem/Employee/Helper/Searchable.php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;

trait Searchable
{
    public $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    protected function applySearch(Builder $query): Builder
    {
        if (empty($this->search)) {
            return $query;
        }

        $databaseDriver = Config::get('database.default');
        // Suchbegriff aufbereiten und trimmen
        $searchString = trim($this->search);

        if (empty($searchString)) {
            return $query;
        }

        if ($databaseDriver === 'mysql') {
            // Boolean-Modus Suchstring vorbereiten
            // + = Wort muss vorkommen (AND-Logik)
            // * = Präfix-Suche (wie LIKE 'wort%')
            $booleanSearchTerm = collect(explode(' ', $searchString)) // Einfach nach Leerzeichen trennen
                ->filter()
                ->map(fn ($term) => '+'.preg_replace('/[^A-Za-z0-9]/', '', $term).'*') // Normalisieren und für Boolean Mode formatieren
                ->implode(' ');

            if (! empty($booleanSearchTerm)) {
                try {
                    $query->whereRaw(
                        // Qualifiziere alle Spalten mit dem Tabellennamen, um Mehrdeutigkeiten zu vermeiden
                        'MATCH(users.name, users.last_name, users.email, users.phone_1) AGAINST (? IN BOOLEAN MODE)',
                        [$booleanSearchTerm]
                    );
                } catch (\Exception $e) {
                    // Fallback zur LIKE-Suche bei Fehlern (z.B. fehlender FULLTEXT-Index)
                    \Illuminate\Support\Facades\Log::warning('FULLTEXT search failed, falling back to LIKE: ' . $e->getMessage());
                    return $this->applyLegacySearch($query, $searchString);
                }
            }

        } elseif ($databaseDriver === 'pgsql') {
            // PostgreSQL Full-Text Suche (Beispiel, ggf. anpassen!)
            // Benötigt oft eine tsvector Spalte und einen GIN/GiST Index darauf.
            // Die Abfrage verwendet dann z.B. to_tsquery und den @@ Operator.
            // Beispiel (sehr vereinfacht):
            $searchTerm = str_replace(' ', ' & ', $searchString); // Einfache AND-Logik

            // Annahme: Du hast eine 'tsv_search_column' vom Typ tsvector
            // $query->whereRaw("tsv_search_column @@ to_tsquery('german', ?)", [$searchTerm]);
            // --> Hier müsstest du die PostgreSQL-spezifische Implementierung recherchieren!
            // Als Fallback erstmal die alte LIKE-Logik (oder Fehler werfen)
            return $this->applyLegacySearch($query, $searchString); // Siehe unten

        } else {
            // Fallback für andere DBs (alte LIKE-Logik)
            return $this->applyLegacySearch($query, $searchString); // Siehe unten
        }

        return $query;
    }

    /**
     * Hilfsmethode für die alte LIKE-basierte Suche (Fallback)
     */
    protected function applyLegacySearch(Builder $query, string $searchString): Builder
    {
        $terms = collect(str_getcsv($searchString, ' ', '"'))->filter();
        $query->where(function ($q) use ($terms) {
            foreach ($terms as $term) {
                $likeTerm = $term.'%'; // Einfaches Präfix für Fallback
                $q->where(function ($innerQ) use ($likeTerm) {
                    // Auch hier alle Spalten mit Tabellennamen qualifizieren
                    $innerQ->orWhere('users.name', 'like', $likeTerm)
                           ->orWhere('users.last_name', 'like', $likeTerm)
                           ->orWhere('users.email', 'like', $likeTerm)
                           ->orWhere('users.phone_1', 'like', $likeTerm);
                });
            }
        });

        return $query;
    }
}
