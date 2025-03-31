<?php

use App\Enums\Model\ModelStatus;
use App\Enums\User\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Erstellt die Tabellen für Benutzer, Passwort-Reset-Tokens und Sitzungen.
     * Unterstützt MySQL und PostgreSQL mit optimierten datenbankspezifischen Implementierungen.
     */
    public function up(): void
    {
        // SQLite wird nicht unterstützt
        if (config('database.default') === 'sqlite') {
            throw new \Exception('Diese Anwendung unterstützt SQLite nicht mehr. Bitte verwenden Sie MySQL oder PostgreSQL.');
        }

        // MySQL-spezifische Implementation mit virtuellen Spalten für normalisierte Felder
        if (config('database.default') === 'mysql') {
            Schema::create('users', function (Blueprint $table) {
                // Primärschlüssel und Identifikation
                $table->id();
                $table->string('slug')->unique()->nullable();

                // Persönliche Informationen
                $table->string('name');
                $table->string('name_normalized')
                    ->virtualAs("regexp_replace(lower(name), '[^a-z0-9]', '')")
                    ->nullable()->index();
                $table->string('last_name')->nullable();
                $table->string('last_name_normalized')
                    ->virtualAs("regexp_replace(lower(last_name), '[^a-z0-9]', '')")
                    ->nullable()->index();
                $table->string('gender')->default(\App\Enums\User\Gender::Male)->nullable();
                $table->string('phone_1')->nullable();
                $table->string('phone_2')->nullable();

                // Organisations- und Rollenzuordnung
                $table->foreignId('company_id')->nullable()->index(); // Wird später durch Constraint ergänzt
                $table->foreignId('department_id')->nullable(); // Wird später durch Constraint ergänzt
                $table->string('user_type')->default(UserType::Employee);
                $table->string('model_status')->default(ModelStatus::ACTIVE);
                $table->timestamp('joined_at')->nullable();
                $table->foreignId('created_by')->nullable(); // Selbstreferenz wird später durch Constraint ergänzt

                // Authentifizierung und Sicherheit
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();

                // UI-Präferenzen
                $table->string('theme')->default('default');
                $table->foreignId('current_team_id')->nullable();
                $table->string('profile_photo_path', 2048)->nullable();

                // System-Zeitstempel
                $table->softDeletes();
                $table->timestamps();

                // Optimierte Indizes für häufig abgefragte Felder
                $table->index(['user_type', 'model_status'], 'idx_user_type_status');
                $table->index(['company_id', 'department_id'], 'idx_company_department');
            });
        }
        
        // PostgreSQL-spezifische Implementation mit rawIndex für normalisierte Felder
        if (config('database.default') === 'pgsql') {
            Schema::create('users', function (Blueprint $table) {
                // Primärschlüssel und Identifikation
                $table->id();
                $table->string('slug')->unique()->nullable();

                // Persönliche Informationen
                $table->string('name');
                $table->string('last_name')->nullable();
                $table->string('gender')->default(\App\Enums\User\Gender::Male)->nullable();
                $table->string('phone_1')->nullable();
                $table->string('phone_2')->nullable();

                // Organisations- und Rollenzuordnung
                $table->foreignId('company_id')->nullable()->index(); // Wird später durch Constraint ergänzt
                $table->foreignId('department_id')->nullable(); // Wird später durch Constraint ergänzt
                $table->string('user_type')->default(UserType::Employee);
                $table->string('model_status')->default(ModelStatus::ACTIVE);
                $table->timestamp('joined_at')->nullable();
                $table->foreignId('created_by')->nullable(); // Selbstreferenz wird später durch Constraint ergänzt

                // Authentifizierung und Sicherheit
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();

                // UI-Präferenzen
                $table->string('theme')->default('default');
                $table->foreignId('current_team_id')->nullable();
                $table->string('profile_photo_path', 2048)->nullable();

                // System-Zeitstempel
                $table->softDeletes();
                $table->timestamps();

                // Optimierte Indizes für häufig abgefragte Felder
                $table->index(['user_type', 'model_status'], 'idx_user_type_status');
                $table->index(['company_id', 'department_id'], 'idx_company_department');

                // PostgreSQL-spezifische normalisierte Indizes für optimierte Suche
                $table->rawIndex("regexp_replace(lower(name), '[^a-z0-9]', '')", 'users_name_normalized_index');
                $table->rawIndex("regexp_replace(lower(last_name), '[^a-z0-9]', '')", 'users_last_name_normalized_index');
            });
        }

        /* 
        // Hier ist die auskommentierte PostgreSQL-spezifische Version für zukünftige Referenz
        // Diese Version nutzt PostgreSQL-eigene Funktionen für bessere Suchperformance
        
        if (config('database.default') === 'pgsql') {
            // Index-Erstellung für normalisierte Suche in PostgreSQL
            DB::statement('CREATE INDEX users_name_normalized_idx ON users USING btree (regexp_replace(lower(name), \'[^a-z0-9]\', \'\'))');
            DB::statement('CREATE INDEX users_last_name_normalized_idx ON users USING btree (regexp_replace(lower(last_name), \'[^a-z0-9]\', \'\'))');
            
            // Alternativ: Materialized View für optimierte Suche erstellen
            DB::statement('
                CREATE MATERIALIZED VIEW IF NOT EXISTS user_search_index AS
                SELECT 
                    id, 
                    regexp_replace(lower(name), \'[^a-z0-9]\', \'\') as name_normalized,
                    regexp_replace(lower(last_name), \'[^a-z0-9]\', \'\') as last_name_normalized,
                    lower(email) as email_lower,
                    phone_1
                FROM users
            ');
            
            // Indizes auf dem Materialized View
            DB::statement('CREATE INDEX user_search_name_idx ON user_search_index USING btree (name_normalized)');
            DB::statement('CREATE INDEX user_search_last_name_idx ON user_search_index USING btree (last_name_normalized)');
            DB::statement('CREATE INDEX user_search_email_idx ON user_search_index USING btree (email_lower)');
            
            // Trigger für automatische Aktualisierung des Views bei Änderungen
            DB::unprepared('
                CREATE OR REPLACE FUNCTION refresh_user_search_index()
                RETURNS TRIGGER AS $$
                BEGIN
                    REFRESH MATERIALIZED VIEW CONCURRENTLY user_search_index;
                    RETURN NULL;
                END;
                $$ LANGUAGE plpgsql;
                
                DROP TRIGGER IF EXISTS refresh_user_search_trigger ON users;
                
                CREATE TRIGGER refresh_user_search_trigger
                AFTER INSERT OR UPDATE OR DELETE ON users
                FOR EACH STATEMENT
                EXECUTE FUNCTION refresh_user_search_index();
            ');
        }
        */

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     * Entfernt die erstellten Tabellen.
     */
    public function down(): void
    {
        // Für PostgreSQL: Materialized View aufräumen, falls er existiert
        if (config('database.default') === 'pgsql') {
            // Auskommentiert, da der oben stehende Code ebenfalls auskommentiert ist
            // DB::statement('DROP MATERIALIZED VIEW IF EXISTS user_search_index');
        }
        
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
