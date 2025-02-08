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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->foreignId('owner_id');
            $table->foreignId('created_by');
            $table->foreignId('industry_id')->constrained('industries');
            $table->string('company_url')->nullable()->unique();
            $table->enum('company_size', ['1-5', '6-10', '11-20', '21-50','51-100','101-200','>200',])->default('1-5');
            $table->enum('company_type', ['ag', 'einzelfirma', 'gmbh'])->default('gmbh');
            $table->string('register_number')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->enum('registration_type', ['Registerform', 'Adminform'])->default('Registerform');
            $table->boolean('is_active')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }

};
