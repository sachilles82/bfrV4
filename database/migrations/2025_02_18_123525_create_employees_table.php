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
            $table->date('date_hired')->nullable();
            $table->date('date_fired')->nullable();
            $table->date('probation')->nullable();
            $table->string('probation_enum')->default(Probation::THREE_MONTHS->value);
            $table->string('social_number')->nullable();
            $table->string('personal_number')->nullable();
            $table->string('profession')->nullable();
            $table->string('stage')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('notice_period')->nullable();
            $table->string('notice_period_enum')->default(NoticePeriod::THREE_MONTHS->value);
            $table->string('employee_status')->default(EmployeeStatus::PROBATION->value);
            $table->timestamps();
        });
    }
};
