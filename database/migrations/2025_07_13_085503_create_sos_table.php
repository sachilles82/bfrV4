<?php

use App\Enums\User\Gender;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     * Erstellt die Tabelle für SOS-Kontakte.
     */
    public function up(): void
    {
        Schema::create('sos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('gender')->default(Gender::Male)->nullable();
            $table->string('related');
            $table->string('phone');
            $table->string('email')->unique();

            $table->timestamps();
        });
    }
};
