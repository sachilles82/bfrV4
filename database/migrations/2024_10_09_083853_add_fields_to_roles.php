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
            $table->foreignId('created_by')->index()->constrained('users')->cascadeOnDelete();
            $table->text('description')->nullable()->after('created_by');
            $table->foreignId('company_id')->index()->nullable()->after('created_by')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->after('company_id')->constrained('teams')->cascadeOnDelete();
            $table->string('access')->index()->after('description');
            $table->string('visible')->index()->after('access');
            $table->boolean('is_manager')->default(false)->after('visible');
            $table->index(['company_id', 'access', 'visible']);
        });
    }

};
