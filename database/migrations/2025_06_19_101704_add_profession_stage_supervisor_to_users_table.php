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
        Schema::table('users', function (Blueprint $table) {
            $table->after('department_id', function ($table) {
                $table->foreignId('profession_id')
                    ->nullable()
                    ->constrained('professions')
                    ->cascadeOnDelete();

                $table->foreignId('stage_id')
                    ->nullable()
                    ->constrained('stages')
                    ->cascadeOnDelete();

                $table->foreignId('supervisor_id')
                    ->nullable()
                    ->constrained('users')
                    ->cascadeOnDelete();
            });

            // Indices werden automatisch von foreignId() erstellt
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
