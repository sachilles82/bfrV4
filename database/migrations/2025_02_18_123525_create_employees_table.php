<?php

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Erstellt die employees-Tabelle mit optimierten Fremdschlüsselbeziehungen
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('personal_number')->nullable();
            $table->string('employment_type')->nullable();

            $table->string('probation_enum')->default(Probation::THREE_MONTHS->value);
            $table->date('probation_at')->nullable();
            $table->string('notice_at')->nullable();
            $table->string('notice_enum')->default(NoticePeriod::ONE_MONTH->value);
            $table->date('leave_at')->nullable();

            $table->string('ahv_number')->nullable();
            $table->string('nationality')->nullable();
            $table->string('hometown')->nullable();
            $table->string('religion')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('residence_permit')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * Entfernt die employees-Tabelle
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
