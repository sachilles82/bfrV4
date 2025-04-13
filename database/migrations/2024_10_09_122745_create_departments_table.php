<?php

use App\Enums\Model\ModelStatus;
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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index(); // Bestehender Index
            $table->text('description')->nullable();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->index()->constrained()->cascadeOnDelete(); // Bestehender Index
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('model_status')->default(ModelStatus::ACTIVE);
            $table->softDeletes(); // Fügt die deleted_at Spalte hinzu
            $table->timestamps();

            // *** NEU hinzugefügter zusammengesetzter Index ***
            $table->index(['team_id', 'model_status', 'deleted_at'], 'departments_team_status_deleted_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Die down-Methode bleibt unverändert, da sie die gesamte Tabelle löscht.
        Schema::dropIfExists('departments');
    }
};
