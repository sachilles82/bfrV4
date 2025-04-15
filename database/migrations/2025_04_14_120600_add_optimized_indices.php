<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fügt optimierte Indizes zu bestehenden Tabellen hinzu.
     */
    public function up(): void
    {
        // Optimierter Index für model_has_roles (verbessert HasRoles.php#241 Abfrage)
        Schema::table('model_has_roles', function (Blueprint $table) {
            // Falls der Index noch nicht existiert
            if (! Schema::hasIndex('model_has_roles', 'idx_model_roles_type_id')) {
                $table->index(['model_id', 'model_type'], 'idx_model_roles_type_id');
            }
        });

        // Optimierter Index für model_has_permissions (verbessert HasPermissions.php#328 Abfrage)
        Schema::table('model_has_permissions', function (Blueprint $table) {
            // Falls der Index noch nicht existiert
            if (! Schema::hasIndex('model_has_permissions', 'idx_model_perms_type_id')) {
                $table->index(['model_id', 'model_type'], 'idx_model_perms_type_id');
            }
        });

        // Optimierter Index für team_user (verbessert EmployeeTable.php Abfrage zu Teams)
        Schema::table('team_user', function (Blueprint $table) {
            // Falls der Index noch nicht existiert
            if (! Schema::hasIndex('team_user', 'idx_team_user_userid')) {
                $table->index('user_id', 'idx_team_user_userid');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Entfernt die hinzugefügten Indizes.
     */
    public function down(): void
    {
        //        Schema::table('model_has_roles', function (Blueprint $table) {
        //            $table->dropIndex('idx_model_roles_type_id');
        //        });
        //
        //        Schema::table('model_has_permissions', function (Blueprint $table) {
        //            $table->dropIndex('idx_model_perms_type_id');
        //        });
        //
        //        Schema::table('team_user', function (Blueprint $table) {
        //            $table->dropIndex('idx_team_user_userid');
        //        });
    }
};
