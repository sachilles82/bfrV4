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
            $table->foreignId('created_by')->after('user_type')->nullable();
            $table->string('theme')->default('default')->after('created_by');

        });
    }
};
