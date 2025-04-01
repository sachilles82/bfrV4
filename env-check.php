<?php
// Redis-Umgebungsvariablen prüfen
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

// Cache-Konfiguration prüfen
echo "=== CACHE KONFIGURATION ===\n";
echo "CACHE_STORE: " . env('CACHE_STORE', 'Nicht gesetzt, Fallback auf config/cache.php: ' . config('cache.default')) . "\n";
echo "Default Cache-Treiber: " . config('cache.default') . "\n";

// Redis-Konfiguration prüfen
echo "\n=== REDIS KONFIGURATION ===\n";
echo "REDIS_CLIENT: " . env('REDIS_CLIENT', 'Nicht gesetzt, Fallback auf config/database.php: ' . config('database.redis.client')) . "\n";
echo "Aktueller Redis-Client: " . config('database.redis.client') . "\n";
echo "REDIS_HOST: " . env('REDIS_HOST', '127.0.0.1') . "\n";
echo "REDIS_PORT: " . env('REDIS_PORT', '6379') . "\n";
echo "REDIS_PASSWORD: " . (env('REDIS_PASSWORD') ? '******' : 'Nicht gesetzt') . "\n";

// Permission-Cache-Konfiguration prüfen
echo "\n=== PERMISSION CACHE KONFIGURATION ===\n";
echo "Permission Cache Store: " . config('permission.cache.store') . "\n";

// Redis-Verbindungstest
echo "\n=== REDIS VERBINDUNGSTEST ===\n";
try {
    if (extension_loaded('redis')) {
        echo "PHP Redis-Erweiterung: Installiert\n";
        
        $redis = new Redis();
        $redis->connect(env('REDIS_HOST', '127.0.0.1'), env('REDIS_PORT', 6379));
        echo "Redis-Server Native Verbindung: " . $redis->ping() . "\n";
    } else {
        echo "PHP Redis-Erweiterung: NICHT installiert\n";
        
        $redis = new Predis\Client([
            'scheme' => 'tcp',
            'host'   => env('REDIS_HOST', '127.0.0.1'),
            'port'   => env('REDIS_PORT', 6379),
            'password' => env('REDIS_PASSWORD'),
        ]);
        
        echo "Redis-Server Predis Verbindung: " . $redis->ping() . "\n";
    }
} catch (Exception $e) {
    echo "Fehler bei der Redis-Verbindung: " . $e->getMessage() . "\n";
}

// Laravel Cache-Test
echo "\n=== LARAVEL CACHE TEST ===\n";
try {
    $testKey = 'redis-test-' . uniqid();
    $testValue = 'Erfolgreich am ' . date('Y-m-d H:i:s');
    
    // Cache-Wert setzen
    cache([$testKey => $testValue], 60);
    
    // Cache-Wert abrufen
    $retrievedValue = cache($testKey);
    
    echo "Wert gesetzt: $testValue\n";
    echo "Wert abgerufen: $retrievedValue\n";
    echo "Cache-Test: " . ($testValue === $retrievedValue ? "ERFOLGREICH" : "FEHLGESCHLAGEN") . "\n";
} catch (Exception $e) {
    echo "Fehler beim Cache-Test: " . $e->getMessage() . "\n";
}
