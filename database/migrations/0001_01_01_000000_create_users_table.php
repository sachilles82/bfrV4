<?php

use App\Enums\Model\ModelStatus;
use App\Enums\User\Gender;
use App\Enums\User\UserType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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

        // --- MySQL-spezifische Implementation ---
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
                $table->string('gender')->default(Gender::Male)->nullable();
                $table->string('phone_1')->nullable();
                $table->string('phone_2')->nullable();

                // Organisations- und Rollenzuordnung
                $table->foreignId('company_id')->nullable();
                $table->foreignId('department_id')->nullable();
                $table->string('user_type')->default(UserType::Employee);
                $table->string('model_status')->default(ModelStatus::ACTIVE);
                $table->timestamp('joined_at')->nullable();
                $table->foreignId('created_by')->nullable();

                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();

                $table->string('theme')->default('default');
                $table->foreignId('current_team_id')->nullable();
                $table->string('profile_photo_path', 2048)->nullable();

                $table->softDeletes();
                $table->index('deleted_at');
                $table->timestamps();

                // --- Optimierte Indizes für häufig abgefragte Felder ---
                $table->index(['user_type', 'model_status', 'deleted_at'], 'idx_user_type_status_deleted');
                $table->index(['company_id', 'department_id'], 'idx_company_department');

                // Indexes für die Sortierfelder
                $table->index('name');
                $table->index('joined_at');
                $table->index('created_at');

            }); // Ende Schema::create für MySQL

            // *** NEU hinzugefügter FULLTEXT Index (MySQL-spezifisch) ***
            // Muss nach der Tabellenerstellung hinzugefügt werden
            DB::statement('ALTER TABLE users ADD FULLTEXT INDEX users_search_original_fulltext_idx (name, last_name, email, phone_1)');

        } // Ende if MySQL

        //        // --- PostgreSQL-spezifische Implementation ---
        //        if (config('database.default') === 'pgsql') {
        //            Schema::create('users', function (Blueprint $table) {
        //                // Primärschlüssel und Identifikation
        //                $table->id();
        //                $table->string('slug')->unique()->nullable();
        //
        //                // Persönliche Informationen
        //                $table->string('name');
        //                $table->string('last_name')->nullable();
        //                $table->string('gender')->default(Gender::Male)->nullable();
        //                $table->string('phone_1')->nullable(); // Spalte existiert bereits
        //                $table->string('phone_2')->nullable();
        //
        //                // Organisations- und Rollenzuordnung
        //                $table->foreignId('company_id')->nullable();
        //                $table->foreignId('department_id')->nullable();
        //                $table->string('user_type')->default(UserType::Employee);
        //                $table->string('model_status')->default(ModelStatus::ACTIVE);
        //                $table->timestamp('joined_at')->nullable(); // Spalte existiert bereits
        //                $table->foreignId('created_by')->nullable();
        //
        //                // Authentifizierung und Sicherheit
        //                $table->string('email')->unique();
        //                $table->timestamp('email_verified_at')->nullable();
        //                $table->string('password');
        //                $table->rememberToken();
        //
        //                // UI-Präferenzen
        //                $table->string('theme')->default('default');
        //                $table->foreignId('current_team_id')->nullable();
        //                $table->string('profile_photo_path', 2048)->nullable();
        //
        //                // System-Zeitstempel
        //                $table->softDeletes();
        //                $table->timestamps();
        //
        //                // --- Optimierte Indizes für häufig abgefragte Felder ---
        //                // Bestehende Indizes
        //                $table->index(['user_type', 'model_status'], 'idx_user_type_status');
        //                $table->index(['company_id', 'department_id'], 'idx_company_department');
//        $table->index('name');
//        $table->index('joined_at');
//        $table->index('created_at');
        //
        //                // PostgreSQL-spezifische normalisierte Indizes
        //                $table->rawIndex("regexp_replace(lower(name), '[^a-z0-9]', '')", 'users_name_normalized_index');
        //                $table->rawIndex("regexp_replace(lower(last_name), '[^a-z0-9]', '')", 'users_last_name_normalized_index');
        //
        //                // *** NEU hinzugefügte Standard-Indizes ***
        //
        //
        //                // Hinweis: Der MySQL FULLTEXT Index ist hier nicht direkt anwendbar.
        //                // PostgreSQL benötigt eine andere Konfiguration für Volltextsuche (z.B. tsvector, GIN).
        //            }); // Ende Schema::create für PostgreSQL
        //        } // Ende if PostgreSQL
        //
        //        // --- Erstellung der anderen Tabellen (unverändert) ---
        //        Schema::create('password_reset_tokens', function (Blueprint $table) {
        //            $table->string('email')->primary();
        //            $table->string('token');
        //            $table->timestamp('created_at')->nullable();
        //        });
        //
        //        Schema::create('sessions', function (Blueprint $table) {
        //            $table->string('id')->primary();
        //            $table->foreignId('user_id')->nullable()->index();
        //            $table->string('ip_address', 45)->nullable();
        //            $table->text('user_agent')->nullable();
        //            $table->longText('payload');
        //            $table->integer('last_activity')->index();
        //        });
    }
};
