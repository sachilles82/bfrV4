<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('state_id')->constrained()->cascadeOnDelete();
//            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnDelete();
//            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['name', 'state_id', 'team_id', 'created_by']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
