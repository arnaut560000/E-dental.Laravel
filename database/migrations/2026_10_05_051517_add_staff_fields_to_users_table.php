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
        $table->string('username')->nullable()->unique()->after('name');
        $table->string('role')->default('scheduler')->after('password'); // admin | scheduler | service_desk
        $table->boolean('is_active')->default(true);
        $table->boolean('must_change_password')->default(false);
        $table->timestamp('temporary_password_expires_at')->nullable();
        $table->unsignedBigInteger('last_seen_appointment_id')->default(0);
        $table->string('email')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'username', 'role', 'is_active', 'must_change_password',
            'temporary_password_expires_at', 'last_seen_appointment_id',
        ]);
    });
}
};