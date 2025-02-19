<?php

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
            $table->string('user_type')->default(UserType::Employee)->after('company_id');
            $table->string('gender')->default(\App\Enums\User\Gender::Male)->after('user_type')->nullable();
            $table->foreignId('created_by')->after('gender')->nullable();
            $table->string('theme')->default('default')->after('created_by');
            $table->string('slug')->unique()->nullable()->after('theme');
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
