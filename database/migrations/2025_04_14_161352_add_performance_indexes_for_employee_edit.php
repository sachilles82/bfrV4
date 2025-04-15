<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Führt die Migrationen aus.
     * Fügt Indizes für verbesserte Abfrageleistung beim Laden des Employee-Edit-Modals hinzu.
     */
    public function up(): void
    {
        //        // Index für die schnellere Supervisor-Abfrage
        //        Schema::table('roles', function (Blueprint $table) {
        //            // Verbessert die Abfrage nach Manager-Rollen
        //            $table->index('is_manager', 'idx_roles_is_manager');
        //
        //            // Beschleunigt die Suche nach bestimmten Rollen-Typen
        //            $table->index(['access', 'visible'], 'idx_roles_access_visible');
        //        });
        //
        //        // Index für schnellere Department-Filterung
        //        Schema::table('departments', function (Blueprint $table) {
        //            // Optimiert die Abfrage nach Departments eines bestimmten Teams
        //            $table->index(['team_id', 'model_status'], 'idx_departments_team_status');
        //
        //            // Beschleunigt Suche nach aktiven Departments in einer Company
        //            $table->index(['company_id', 'model_status'], 'idx_departments_company_status');
        //        });
        //
        //        // Verbessert die Performance bei Beziehungsabfragen
        //        Schema::table('model_has_roles', function (Blueprint $table) {
        //            // Optimiert die Abfrage nach Usern mit bestimmten Rollen
        //            $table->index(['model_type', 'model_id'], 'idx_model_has_roles_model');
        //        });
    }

    /**
     * Macht die Migrationen rückgängig.
     */
    public function down(): void
    {
        //        Schema::table('roles', function (Blueprint $table) {
        //            $table->dropIndex('idx_roles_is_manager');
        //            $table->dropIndex('idx_roles_access_visible');
        //        });
        //
        //        Schema::table('departments', function (Blueprint $table) {
        //            $table->dropIndex('idx_departments_team_status');
        //            $table->dropIndex('idx_departments_company_status');
        //        });
        //
        //        Schema::table('model_has_roles', function (Blueprint $table) {
        //            $table->dropIndex('idx_model_has_roles_model');
        //        });
    }
};
