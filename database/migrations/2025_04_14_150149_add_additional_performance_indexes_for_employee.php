<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Führt die Migrationen aus.
     * Fügt zusätzliche Indizes zur weiteren Optimierung der Employee-Komponenten hinzu.
     * 
     * @return void
     */
    public function up(): void
    {
        // Indizes für die users-Tabelle
        Schema::table('users', function (Blueprint $table) {
            // Beschleunigt Abfragen nach Benutzern einer bestimmten Firma
            $table->index('company_id', 'idx_users_company_id');
            
            // Optimiert die Filterung nach Benutzertyp
            $table->index('user_type', 'idx_users_user_type');
            
            // Verbessert die Performance bei Abfragen nach aktiven/inaktiven Benutzern
            $table->index('model_status', 'idx_users_model_status');
        });

        // Indizes für die employees-Tabelle
        Schema::table('employees', function (Blueprint $table) {
            // Beschleunigt die Verknüpfung mit der users-Tabelle
            $table->index('user_id', 'idx_employees_user_id');
            
            // Optimiert die Filterung nach Mitarbeiterstatus
            $table->index('employee_status', 'idx_employees_employee_status');
            
            // Verbessert die Abfrage nach Mitarbeitern mit bestimmten Berufen oder Stufen
            $table->index('profession_id', 'idx_employees_profession_id');
            $table->index('stage_id', 'idx_employees_stage_id');
        });

        // Indizes für die professions- und stages-Tabellen
        Schema::table('professions', function (Blueprint $table) {
            // Beschleunigt die Suche nach Berufen einer bestimmten Firma
            $table->index('company_id', 'idx_professions_company_id');
        });

        Schema::table('stages', function (Blueprint $table) {
            // Beschleunigt die Suche nach Stufen einer bestimmten Firma
            $table->index('company_id', 'idx_stages_company_id');
        });
        
        // Indizes für die team_user-Tabelle
        Schema::table('team_user', function (Blueprint $table) {
            // Optimiert die Abfrage nach Benutzern in einem Team
            $table->index(['team_id', 'user_id'], 'idx_team_user_team_user');
        });
    }

    /**
     * Macht die Migrationen rückgängig.
     *
     * @return void
     */
    public function down(): void
    {
        // Rückgängig machen der Indizes für die users-Tabelle
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_company_id');
            $table->dropIndex('idx_users_user_type');
            $table->dropIndex('idx_users_model_status');
        });

        // Rückgängig machen der Indizes für die employees-Tabelle
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_employees_user_id');
            $table->dropIndex('idx_employees_employee_status');
            $table->dropIndex('idx_employees_profession_id');
            $table->dropIndex('idx_employees_stage_id');
        });

        // Rückgängig machen der Indizes für die professions- und stages-Tabellen
        Schema::table('professions', function (Blueprint $table) {
            $table->dropIndex('idx_professions_company_id');
        });

        Schema::table('stages', function (Blueprint $table) {
            $table->dropIndex('idx_stages_company_id');
        });
        
        // Rückgängig machen der Indizes für die team_user-Tabelle
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropIndex('idx_team_user_team_user');
        });
    }
};
