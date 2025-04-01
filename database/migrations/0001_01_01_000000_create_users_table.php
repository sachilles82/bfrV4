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
