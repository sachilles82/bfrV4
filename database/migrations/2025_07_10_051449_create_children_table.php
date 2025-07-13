<?php

use App\Enums\User\Gender;
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
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            // Beziehung zum Parent (User)
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Kind-spezifische Daten
            $table->string('name');
            $table->string('gender')->default(Gender::Male);
            $table->date('birthdate');
            $table->string('ahv_number', 16)->nullable();
            $table->date('valid_until');

            $table->timestamps();

        });
    }
};
