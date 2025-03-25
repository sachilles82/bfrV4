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
            $table->date('leave_at')->nullable();
            $table->date('probation_at')->nullable();
            $table->string('probation_enum')->default(Probation::THREE_MONTHS->value);
            $table->string('social_number')->nullable();
            $table->string('personal_number')->nullable();
            $table->string('profession')->nullable();
            $table->string('stage')->nullable();
            $table->string('employment_type')->nullable();

            // Zuerst die Spalte erstellen
            $table->unsignedBigInteger('supervisor_id')->nullable();
            // Dann den Foreign Key definieren
            $table->foreign('supervisor_id')->references('id')->on('users')->nullOnDelete();

            $table->string('notice_at')->nullable();
            $table->string('notice_enum')->default(NoticePeriod::THREE_MONTHS->value);
            $table->string('employee_status')->default(EmployeeStatus::PROBATION->value);

            $table->string('ahv_number')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('nationality')->nullable();
            $table->string('hometown')->nullable();
            $table->string('religion')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('residence_permit')->nullable();

            $table->timestamps();

            // Primärer Index für user_id (bereits durch foreignId erstellt)

            // Index für Suchabfragen nach Status
            $table->index('employee_status');

            // Index für Datumsfilterung
            $table->index('birthdate');

            // Zusammengesetzte Indizes für häufige Abfragen
            $table->index(['profession', 'stage']);
            $table->index(['employee_status', 'profession']);

            // Index für Suche nach AHV-Nummer
            $table->index('ahv_number');

            // Indizes für Filterung nach Nationalität und Religion
            $table->index('nationality');
            $table->index('religion');

            // Indexe für häufige Join-Bedingungen
            $table->index(['user_id', 'employee_status']);

            // Index für den Supervisor
            $table->index('supervisor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
