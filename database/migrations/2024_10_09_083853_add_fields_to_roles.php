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
        Schema::table('roles', function (Blueprint $table) {
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('description')->nullable()->after('created_by');
            $table->foreignId('company_id')->nullable()->after('created_by')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->after('company_id')->constrained('teams')->cascadeOnDelete();

            $table->string('access')->after('description');
            $table->string('visible')->after('access');
            $table->boolean('is_manager')->default(false)->after('visible');

            $table->index('created_by');
            $table->index('company_id');
            $table->index('team_id');
            $table->index('access');
            $table->index('visible');
            $table->index('is_manager');
            $table->index(['company_id', 'access', 'visible']);
//            $table->index(['access', 'visible', 'created_by'], 'roles_access_visible_created_by_index');
        });
    }
};
