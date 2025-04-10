<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ÄNDERUNG: Index auf Originalspalten erstellen
        DB::statement('ALTER TABLE users ADD FULLTEXT INDEX users_search_original_fulltext_idx (name, last_name, email, phone_1)');
        // Indexname geändert zur Klarheit
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sicherer Weg, den FULLTEXT Index zu löschen (MySQL)
        DB::statement('ALTER TABLE users DROP INDEX IF EXISTS users_search_original_fulltext_idx');
        // IF EXISTS hinzugefügt, um Fehler beim Rollback zu vermeiden, falls manuell gelöscht
    }
};
