<?php

// Einfaches Skript zum Ändern des REDIS_CLIENT-Werts in der .env-Datei
$envFile = __DIR__.'/.env';
$envContent = file_get_contents($envFile);

// Überprüfen, ob REDIS_CLIENT existiert und enthält phpredis
if (preg_match('/REDIS_CLIENT\s*=\s*phpredis/', $envContent)) {
    // Ersetzen durch predis
    $envContent = preg_replace('/REDIS_CLIENT\s*=\s*phpredis/', 'REDIS_CLIENT=predis', $envContent);
    file_put_contents($envFile, $envContent);
    echo "REDIS_CLIENT wurde erfolgreich auf 'predis' umgestellt.\n";
} else {
    // Falls der Wert nicht phpredis ist oder die Zeile nicht existiert
    if (strpos($envContent, 'REDIS_CLIENT') !== false) {
        // Die Zeile existiert, aber mit einem anderen Wert als phpredis
        $envContent = preg_replace('/REDIS_CLIENT\s*=\s*[^\r\n]*/', 'REDIS_CLIENT=predis', $envContent);
        file_put_contents($envFile, $envContent);
        echo "REDIS_CLIENT wurde auf 'predis' aktualisiert.\n";
    } else {
        // Die Zeile existiert nicht
        $envContent .= "\nREDIS_CLIENT=predis\n";
        file_put_contents($envFile, $envContent);
        echo "REDIS_CLIENT=predis wurde zur .env-Datei hinzugefügt.\n";
    }
}

// Cache-Konfiguration leeren (optional)
echo "Führen Sie 'php artisan config:clear' aus, um die Änderungen zu übernehmen.\n";
