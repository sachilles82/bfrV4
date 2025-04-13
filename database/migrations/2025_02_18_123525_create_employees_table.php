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
            $table->foreignId('user_id')->index()->constrained('users')->cascadeOnDelete();
            $table->uuid('uuid')->nullable()->unique();

            $table->string('personal_number')->nullable();
            $table->foreignId('profession_id')->constrained('professions')->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained('stages')->cascadeOnDelete();
            $table->string('employment_type')->nullable();
            // Verwende die moderne foreignId-Methode mit constrained und cascadeOnDelete
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('probation_enum')->default(Probation::THREE_MONTHS->value);
            $table->date('probation_at')->nullable();
            $table->string('notice_at')->nullable();
            $table->string('notice_enum')->default(NoticePeriod::ONE_MONTH->value);
            $table->date('leave_at')->nullable();
            $table->string('employee_status')->default(EmployeeStatus::PROBATION->value);

            $table->string('ahv_number')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('nationality')->nullable();
            $table->string('hometown')->nullable();
            $table->string('religion')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('residence_permit')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'employee_status'], 'idx_user_status');
            $table->index(['profession_id', 'stage_id'], 'idx_profession_stage');
            $table->index(['employee_status', 'profession_id'], 'idx_status_profession');

            $table->index('supervisor_id', 'idx_supervisor');
            $table->index('employee_status', 'idx_employee_status');
            $table->index('birthdate', 'idx_birthdate');
            $table->index('ahv_number', 'idx_ahv_number');
            $table->index('nationality', 'idx_nationality');
            $table->index('religion', 'idx_religion');
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
