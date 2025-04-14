<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Service Provider für optimiertes Berechtigungs-Caching.
 * 
 * Dieser Service Provider erweitert das Spatie Permission Package
 * um effizientes Caching der Berechtigungen und Rollen, um Datenbankabfragen
 * deutlich zu reduzieren.
 */
class PermissionCacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Cache für gesamten Permission-Bereich (24 Stunden)
        $cacheDuration = 60 * 24;
        
        // Alle Rollen beim Anwendungsstart cachen,
        // was gut funktioniert, da Rollen selten geändert werden
        Cache::remember('all_roles', $cacheDuration, function () {
            return Role::with('permissions')->get();
        });
        
        // Permission-Cache erneuern bei Änderungen
        Event::listen(['role-updated', 'permission-updated', 'role-permission-updated'], function () {
            Cache::forget('all_roles');
        });
        
        // Methode erweitern, um gecachte Berechtigungen zu nutzen
        \Illuminate\Foundation\Auth\User::resolveRelationUsing('cachedRoles', function ($user) use ($cacheDuration) {
            $userId = $user->id;
            $cacheKey = "user.{$userId}.roles";
            
            return Cache::remember($cacheKey, $cacheDuration, function () use ($user) {
                return $user->roles()->get();
            });
        });
        
        \Illuminate\Foundation\Auth\User::resolveRelationUsing('cachedPermissions', function ($user) use ($cacheDuration) {
            $userId = $user->id;
            $cacheKey = "user.{$userId}.permissions";
            
            return Cache::remember($cacheKey, $cacheDuration, function () use ($user) {
                return $user->getAllPermissions();
            });
        });
    }
}
