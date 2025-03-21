<?php

use App\Enums\Model\ModelStatus;
use App\Enums\User\UserType;
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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->after('name')->nullable()->constrained('companies')->cascadeOnDelete()->index();
            $table->foreignId('created_by')->after('gender')->nullable();
            $table->string('user_type')->default(UserType::Employee)->after('company_id');
            $table->string('gender')->default(\App\Enums\User\Gender::Male)->after('user_type')->nullable();
            $table->string('theme')->default('default')->after('created_by');
            $table->string('slug')->unique()->nullable()->after('theme');
            $table->string('last_name')->after('slug')->nullable();
            $table->string('model_status')->default(ModelStatus::ACTIVE)->after('last_name');
            $table->string('phone_1')->after('model_status')->nullable();
            $table->string('phone_2')->after('phone_1')->nullable();
            $table->timestamp('joined_at')->after('phone_2')->nullable();
            $table->foreignId('department_id')->nullable()->after('joined_at')->constrained('departments')->nullOnDelete();


            $table->index(['name', 'company_id', 'team_id', 'created_by','user_type', 'model_status','department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
