<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete(); // Wenn User gelöscht wird, werden zugehörige Stages ebenfalls gelöscht
            $table->timestamps();

            $table->index('company_id');
            $table->index('created_by');
        });
    }
};
