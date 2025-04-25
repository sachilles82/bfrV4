<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Erstellt die cities-Tabelle mit optimierten Fremdschlüsselbeziehungen
     */
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('state_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('state_id');
            $table->index('team_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     * Entfernt die cities-Tabelle
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
