<?php

namespace App\Traits\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait UserHasURL
{
    /**
     * Boot the trait
     */
    public static function bootUserHasURL(): void
    {
        static::creating(function (Model $model) {
            if (empty($model->url_slug)) {
                $model->url_slug = $model->generateUniqueSlug();
            }
        });

        static::updating(function (Model $model) {
            // Nur aktualisieren wenn sich der Name geändert hat und kein custom slug gesetzt wurde
            if ($model->isDirty('name') && !$model->hasCustomSlug()) {
                $model->url_slug = $model->generateUniqueSlug();
            }
        });
    }

    /**
     * Generiert einen eindeutigen Slug basierend auf dem Namen
     */
    public function generateUniqueSlug(): string
    {
        $baseSlug = Str::slug($this->name);
        $slug = $baseSlug;
        $counter = 1;

        // Prüfe ob Slug bereits existiert (außer bei eigenem Record)
        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Prüft ob ein Slug bereits existiert
     */
    protected function slugExists(string $slug): bool
    {
        $query = static::where('url_slug', $slug);

        // Bei Update: Eigene ID ausschließen
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return $query->exists();
    }

    /**
     * Prüft ob ein custom Slug gesetzt wurde
     * (Verhindert automatisches Überschreiben von manuell gesetzten Slugs)
     */
    public function hasCustomSlug(): bool
    {
        // Wenn der Slug nicht dem automatisch generierten Pattern entspricht
        $autoPattern = '/^' . Str::slug($this->getOriginal('name')) . '(-\d+)?$/';
        return !preg_match($autoPattern, $this->url_slug);
    }

    /**
     * Setzt einen benutzerdefinierten Slug
     */
    public function setCustomSlug(string $slug): void
    {
        $this->url_slug = Str::slug($slug);
        $this->save();
    }

    /**
     * Generiert die vollständige URL zum Profil
     */
    public function getProfileUrl(): string
    {
        return route('user.profile', ['user' => $this->url_slug]);
    }

    /**
     * Scope für Route Model Binding mit minimalen Feldern
     */
    public function scopeWithSlugFields($query)
    {
        return $query->select([
            'id',
            'url_slug',
            'name', // Für mögliche Slug-Regenerierung
        ]);
    }

    /**
     * Scope für EmployeeProfileController
     * @deprecated Use scopeWithSlugFields() instead
     */
    public function scopeUserEmployeeFields($query)
    {
        return $this->scopeWithSlugFields($query);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKey(): mixed
    {
        return $this->url_slug;
    }

    /**
     * Get the route key name for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'url_slug'; // Korrektur: Muss 'url_slug' sein, nicht 'slug'
    }

//    /**
//     * Resolve Route Model Binding mit optimierter Query
//     */
//    public function resolveRouteBinding($value, $field = null)
//    {
//        $field = $field ?: $this->getRouteKeyName();
//
//        return $this->withSlugFields()
//            ->where($field, $value)
//            ->first();
//    }

// In UserHasURL Trait
    public static function findByUrlSlug($slug)
    {
        \Log::info('findByUrlSlug called', [
            'slug' => $slug,
            'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
        ]);

        return static::where('url_slug', $slug)->first();
    }
}
