<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelLevelSetList;

// use Rector\Laravel\Set\ValueObject\LaravelSetList; // <-- Hinzugefügt

return static function (RectorConfig $rectorConfig): void {
    // ... paths(), skip() etc. ...

    // Aktiviert Regeln für deine PHP-Version
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82, // Oder deine PHP-Version
    ]);

    // Allgemeine Code-Qualität und Dead-Code-Regeln
    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        // ... andere allgemeine Sets ...
    ]);

    // Laravel-spezifische Sets
    $rectorConfig->sets([
        LaravelLevelSetList::UP_TO_LARAVEL_110,
        SetList::CODE_QUALITY,
        //        LaravelSetList::LARAVEL_CODE_QUALITY, // <-- Geändert
        // LaravelSetList::LARAVEL_100,     // <-- Geändert (falls verwendet)
        // LaravelSetList::LARAVEL_110,     // <-- Geändert (falls verwendet)
        //        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER, // <-- Geändert
        // LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL, // <-- Geändert (falls verwendet)
    ]);
    // Optional: Import von Klassen automatisch handhaben
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false); // Kurze Klassennamen (wie Request) nicht automatisch importieren, wenn nicht eindeutig
};
