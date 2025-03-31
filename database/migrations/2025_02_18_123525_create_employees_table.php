<?php

use App\Enums\Employee\EmployeeStatus;
use App\Enums\Employee\NoticePeriod;
use App\Enums\Employee\Probation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('uuid')->nullable()->unique();

            $table->string('personal_number')->nullable();
            $table->foreignId('profession_id')->references('id')->on('professions');
            $table->foreignId('stage_id')->references('id')->on('stages');
            $table->string('employment_type')->nullable();
            // Zuerst die Spalte erstellen
            $table->unsignedBigInteger('supervisor_id')->nullable();
            // Dann den Foreign Key definieren
            $table->foreign('supervisor_id')->references('id')->on('users')->cascadeOnDelete(); // Wenn User gelöscht wird, werden zugehörige Employees ebenfalls gelöscht
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

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
