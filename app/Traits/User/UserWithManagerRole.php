<?php

namespace App\Traits\User;

use Illuminate\Support\Facades\Cache;

trait UserWithManagerRole
{
    /**
     * Check if user has any role with is_manager = true
     */
    public function hasManagerRole(): bool
    {
        return $this->roles()->where('is_manager', true)->exists();
    }

    /**
     * Get manager cache key for company
     */
    public static function getManagerCacheKey(int $companyId): string
    {
        return "company_{$companyId}_managers";
    }

    /**
     * Clear manager cache for company
     */
    public static function clearManagerCache(int $companyId): void
    {
        Cache::forget(self::getManagerCacheKey($companyId));
    }

    /**
     * Get all managers for a company with caching
     */
    public static function getCompanyManagers(int $companyId)
    {
        return Cache::remember(
            self::getManagerCacheKey($companyId),
            60 * 60 * 24, // 24 hours
            function () use ($companyId) {
                return static::query()
                    ->where('company_id', $companyId)
                    ->where('manager', true)
                    ->select(['id', 'name', 'profile_photo_path'])
                    ->orderBy('name')
                    ->get();
            }
        );
    }

    /**
     * Sync manager field based on roles
     * Diese Methode soll aufgerufen werden, wenn Rollen geändert werden
     */
    public function syncManagerStatus(): void
    {
        $hasManagerRole = $this->roles()->where('is_manager', true)->exists();

        // Nur updaten wenn sich der Status ändert
        if ($this->manager !== $hasManagerRole) {
            $this->update(['manager' => $hasManagerRole]);

            // Cache clearen wenn sich Manager Status ändert
            if ($this->company_id) {
                static::clearManagerCache($this->company_id);
            }
        }
    }

    /**
     * Boot method to sync manager status when roles change
     */
    protected static function bootUserWithManagerRole()
    {
        // Wenn Rollen über die Relation synchronisiert werden
        static::updated(function ($user) {
            // Dieser Hook wird nicht automatisch bei Role Sync ausgelöst
            // Daher muss syncManagerStatus() manuell nach Role Sync aufgerufen werden
        });
    }
}
