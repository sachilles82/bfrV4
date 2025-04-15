<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fügt Indizes zu verschiedenen Tabellen hinzu, um die Performance der
     * Mitarbeiter-Tabelle zu verbessern.
     */
    public function up(): void
    {
        // Verbessert die Performance beim Filtern nach Teams
        Schema::table('team_user', function (Blueprint $table) {
            // Füge einen zusammengesetzten Index für user_id und team_id hinzu
            $table->index(['user_id', 'team_id'], 'team_user_user_team_index');
            // Behalte den dedizierten Index für team_id
            $table->index('team_id', 'team_user_team_id_index');
        });
        
        // Verbessert die Performance für die Hauptabfrage in EmployeeTable
        Schema::table('users', function (Blueprint $table) {
            // Kombinierter Index für die häufigsten Filterkriterien
            $table->index(['user_type', 'model_status', 'deleted_at'], 'users_type_status_deleted_index');
            // Index für Sortierung
            $table->index('name', 'users_name_index');
        });
        
        // Verbessert die Joins auf employees Tabelle
        Schema::table('employees', function (Blueprint $table) {
            $table->index('user_id', 'employees_user_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Entfernt die zuvor hinzugefügten Indizes.
     */
    public function down(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropIndex('team_user_user_team_index');
            $table->dropIndex('team_user_team_id_index');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_type_status_deleted_index');
            $table->dropIndex('users_name_index');
        });
        
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('employees_user_id_index');
        });
    }
};
