<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

/**
 * Trait zum Caching von Benutzerberechtigungen und -rollen.
 * 
 * Dieses Trait verbessert die Performance erheblich, indem es die Berechtigungen 
 * und Rollen eines Benutzers im Cache speichert und so wiederholte Datenbankabfragen vermeidet.
 */
trait CachesPermissions
{
    /**
     * Cache-Dauer in Minuten (24 Stunden)
     * 
     * @var int
     */
    protected $permissionCacheDuration = 60 * 24;

    /**
     * Gibt alle Rollen des Benutzers zurück (mit Caching).
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCachedRolesAttribute()
    {
        $cacheKey = "user.{$this->id}.roles";
        
        return Cache::remember($cacheKey, $this->permissionCacheDuration, function () {
            return $this->roles()->get();
        });
    }

    /**
     * Gibt alle Berechtigungen des Benutzers zurück (mit Caching).
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCachedPermissionsAttribute()
    {
        $cacheKey = "user.{$this->id}.permissions";
        
        return Cache::remember($cacheKey, $this->permissionCacheDuration, function () {
            return $this->getAllPermissions();
        });
    }

    /**
     * Überschreibt die Standard-Methode, um gecachte Berechtigungen zu verwenden.
     * 
     * @param string|array $permissions Die zu prüfenden Berechtigungen
     * @return bool
     */
    public function hasPermissionTo($permissions): bool
    {
        // Optimierte Version mit Cache
        if (is_string($permissions)) {
            return $this->cachedPermissions->contains('name', $permissions);
        }
        
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if ($this->cachedPermissions->contains('name', $permission)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Überschreibt die Standard-Methode, um gecachte Rollen zu verwenden.
     * 
     * @param string|array $roles Die zu prüfenden Rollen
     * @return bool
     */
    public function hasRole($roles): bool
    {
        // Optimierte Version mit Cache
        if (is_string($roles)) {
            return $this->cachedRoles->contains('name', $roles);
        }
        
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->cachedRoles->contains('name', $role)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Cache für Berechtigungen und Rollen löschen.
     * Sollte aufgerufen werden, wenn sich Berechtigungen oder Rollen ändern.
     * 
     * @return void
     */
    public function flushPermissionCache(): void
    {
        Cache::forget("user.{$this->id}.roles");
        Cache::forget("user.{$this->id}.permissions");
    }
}
