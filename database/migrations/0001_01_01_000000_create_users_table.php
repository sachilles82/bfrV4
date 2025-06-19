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
        Schema::create('users', function (Blueprint $table) {
            // Primärschlüssel und Identifikation
            $table->id();
            $table->string('url_slug')->unique()->nullable();

            // Persönliche Informationen
            $table->string('name');
            $table->string('name_normalized')->virtualAs("regexp_replace(lower(name), '[^a-z0-9]', '')")->nullable()->index();
            $table->string('gender')->default(Gender::Male)->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();

            // Organisations- und Rollenzuordnung
            $table->foreignId('company_id')->nullable();
            $table->foreignId('department_id')->nullable();
            $table->string('user_type')->default(UserType::Employee);
            $table->string('model_status')->default(ModelStatus::ACTIVE);
            $table->date('joined_at')->nullable();
            $table->foreignId('created_by')->nullable();

            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            $table->string('theme')->default('default');
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();

            $table->softDeletes();
            $table->timestamps();

            // 🚀 OPTIMIERTE INDIZES für deine spezifischen Queries Dieser ist neu für die Profile seite

            // 1. Haupt-Performance Index für User-Lookups mit Relations
            $table->index(['id', 'deleted_at'], 'idx_users_id_soft_delete');

            // 2. Company-spezifische Lookups (sehr häufig verwendet)
            $table->index(['company_id', 'user_type', 'model_status', 'deleted_at'], 'idx_company_user_filtering');

            // 3. Department Relations
            $table->index(['department_id', 'deleted_at'], 'idx_department_soft_delete');

            // 4. Team Relations (für current_team_id)
            $table->index(['current_team_id', 'company_id'], 'idx_current_team_company');

            // 5. Auth & Session Performance
            $table->index(['email', 'deleted_at'], 'idx_email_soft_delete');
            // 🚀 OPTIMIERTE INDIZES für deine spezifischen Queries Dieser ist neu für die Profile seite


            // --- Optimierte Indizes für häufig abgefragte Felder ---
            $table->index(['user_type', 'model_status', 'deleted_at'], 'idx_user_type_status_deleted');
            $table->index(['company_id', 'department_id'], 'idx_company_department');

            // Indexes für die Sortierfelder
            $table->index('name');
            $table->index('joined_at');
            $table->index('created_at');
            $table->index('created_by');
            $table->index('deleted_at');

            // === Index für performante Tabelle ===
            $table->index(['user_type', 'model_status', 'deleted_at', 'created_at'], 'idx_users_filter_sort');


        });
        // *** FULLTEXT Index für das Suchfeld (MySQL-spezifisch) ***
        DB::statement('ALTER TABLE users ADD FULLTEXT INDEX users_search_original_fulltext_idx (name, email, phone_1)');

    }

    public function down(): void
    {
        // Beim Zurückrollen der *create*-Migration wird die ganze Tabelle gelöscht.
        Schema::dropIfExists('users');
        // Falls du in DIESER Migration noch andere Tabellen erstellt hast
        // (z.B. password_reset_tokens, sessions), füge hier auch deren
        // Schema::dropIfExists(...) hinzu.
    }
};
