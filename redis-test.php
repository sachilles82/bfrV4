<?php

try {
    if (extension_loaded('redis')) {
        echo "PHP Redis-Erweiterung ist installiert\n";

        $redis = new Redis;
        $redis->connect('127.0.0.1', 6379);
        echo 'Redis-Server Verbindung: '.$redis->ping()."\n";

    } else {
        echo "PHP Redis-Erweiterung ist NICHT installiert\n";
        echo "Versuche mit Predis...\n";

        // Predis erfordert Composer-Autoloader
        require __DIR__.'/vendor/autoload.php';

        $redis = new Predis\Client([
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379,
        ]);

        echo 'Predis Verbindung: '.$redis->ping()."\n";
    }
} catch (Exception $e) {
    echo 'Fehler bei der Redis-Verbindung: '.$e->getMessage()."\n";
}
