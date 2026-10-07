<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_initial', 5)->nullable();
            $table->date('birth_date');
            $table->string('gender');
            $table->string('barangay');
            $table->string('category');
            $table->string('contact_number');
            $table->string('contact_key')->index();
            $table->string('email')->nullable();
            $table->string('registration_mode')->default('Online');
            $table->boolean('privacy_consent')->default(false);
            $table->timestamp('consent_at')->nullable();
            $table->string('privacy_notice_version')->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('id_document_name')->nullable();
            $table->string('id_document_mime')->nullable();
            $table->string('status')->default('Waiting for schedule')->index();
            $table->text('status_reason')->nullable();
            $table->unsignedBigInteger('scheduled_appointment_id')->nullable()->unique();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_initial', 5)->nullable();
            $table->date('birth_date');
            $table->string('gender');
            $table->string('barangay');
            $table->string('category');
            $table->string('contact_number');
            $table->string('contact_key')->nullable()->index();
            $table->string('email')->nullable();
            $table->string('registration_mode')->default('Form');
            $table->date('appointment_date')->index();
            $table->string('appointment_time', 5);
            $table->string('status')->default('Pending')->index();
            $table->text('status_reason')->nullable();
            $table->text('staff_notes')->nullable();
            $table->string('contact_status')->default('Not contacted');
            $table->boolean('texted')->default(false);
            $table->boolean('called')->default(false);
            $table->timestamps();
        });

        // Only Pending/Approved appointments reserve a slot
        DB::statement(
            "CREATE UNIQUE INDEX idx_appointments_active_slot
             ON appointments (appointment_date, appointment_time)
             WHERE status IN ('Pending', 'Approved')"
        );

        Schema::create('appointment_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id')->index();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->string('old_date')->nullable();
            $table->string('old_time')->nullable();
            $table->string('new_date')->nullable();
            $table->string('new_time')->nullable();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('request_submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('target_user_id')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });

        Schema::create('clinic_settings', function (Blueprint $table) {
            $table->string('setting_key')->primary();
            $table->string('setting_value');
            $table->timestamps();
        });

        Schema::create('blocked_dates', function (Blueprint $table) {
            $table->date('blocked_date')->primary();
            $table->string('reason');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_dates');
        Schema::dropIfExists('clinic_settings');
        Schema::dropIfExists('audit_events');
        Schema::dropIfExists('appointment_history');
        DB::statement('DROP INDEX IF EXISTS idx_appointments_active_slot');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('client_requests');
    }
};