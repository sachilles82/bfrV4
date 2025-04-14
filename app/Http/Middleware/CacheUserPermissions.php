<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware zur Optimierung der Berechtigungsprüfungen durch Caching.
 * 
 * Diese Middleware cached die Berechtigungen und Rollen eines Benutzers
 * beim ersten Zugriff und verwendet sie dann für alle nachfolgenden Anfragen,
 * ohne erneute Datenbankabfragen durchführen zu müssen.
 */
class CacheUserPermissions
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->id;
            
            // Cache-Keys für diesen Benutzer
            $permissionsCacheKey = "user.{$userId}.permissions";
            $rolesCacheKey = "user.{$userId}.roles";
            
            // Cache-Lebensdauer in Minuten (24 Stunden)
            $cacheDuration = 60 * 24;
            
            // Prüfen, ob die Berechtigungen bereits gecached sind
            if (!Cache::has($permissionsCacheKey)) {
                // Berechtigungen aus der Datenbank laden und cachen
                $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
                Cache::put($permissionsCacheKey, $userPermissions, $cacheDuration);
            }
            
            // Prüfen, ob die Rollen bereits gecached sind
            if (!Cache::has($rolesCacheKey)) {
                // Rollen aus der Datenbank laden und cachen
                $userRoles = $user->roles()->pluck('name')->toArray();
                Cache::put($rolesCacheKey, $userRoles, $cacheDuration);
            }
            
            // Die gecachten Daten im User-Objekt zur Verfügung stellen
            // (dies ist optional und hängt von Ihrer Implementierung ab)
            $user->cachedPermissions = Cache::get($permissionsCacheKey);
            $user->cachedRoles = Cache::get($rolesCacheKey);
        }
        
        return $next($request);
    }
}
