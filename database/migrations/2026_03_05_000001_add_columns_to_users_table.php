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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->unique()->after('email');
            $table->string('otp_code')->nullable()->after('phone_number');
            $table->enum('role', ['tenant', 'landlord'])->default('tenant')->after('otp_code');
            $table->json('profile_info')->nullable()->after('role');
            $table->timestamp('otp_expires_at')->nullable()->after('profile_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone_number', 'otp_code', 'role', 'profile_info', 'otp_expires_at']);
        });
    }
};
