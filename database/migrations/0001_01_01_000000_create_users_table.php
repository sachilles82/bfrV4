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
     */
    public function up(): void
    {
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
            $table->string('gender')->default(\App\Enums\User\Gender::Male)->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            
            // Normalisierte Felder für Suche
            if (DB::connection()->getDriverName() === 'sqlite') {
                // SQLite: Reguläre Spalte mit Trigger-basierter Aktualisierung
                $table->string('last_name_normalized')->nullable()->index();
            } else {
                // MySQL/PostgreSQL: Virtuelle Spalte mit automatischer Aktualisierung
                $table->string('last_name_normalized')
                    ->virtualAs("regexp_replace(lower(last_name), '[^a-z0-9]', '')")
                    ->nullable()->index();
            }
            
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

        // SQLite-spezifische Trigger für die normalisierten Spalten
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Trigger für die last_name_normalized Spalte bei INSERT
            DB::unprepared('
                CREATE TRIGGER IF NOT EXISTS insert_last_name_normalized AFTER INSERT ON users FOR EACH ROW
                WHEN NEW.last_name IS NOT NULL
                BEGIN
                    UPDATE users
                    SET last_name_normalized = LOWER(REPLACE(REPLACE(REPLACE(NEW.last_name, " ", ""), "-", ""), ".", ""))
                    WHERE id = NEW.id;
                END;
            ');

            // Trigger für die last_name_normalized Spalte bei UPDATE
            DB::unprepared('
                CREATE TRIGGER IF NOT EXISTS update_last_name_normalized AFTER UPDATE OF last_name ON users FOR EACH ROW
                WHEN NEW.last_name <> OLD.last_name OR (NEW.last_name IS NOT NULL AND OLD.last_name IS NULL)
                BEGIN
                    UPDATE users
                    SET last_name_normalized = LOWER(REPLACE(REPLACE(REPLACE(NEW.last_name, " ", ""), "-", ""), ".", ""))
                    WHERE id = NEW.id;
                END;
            ');
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
