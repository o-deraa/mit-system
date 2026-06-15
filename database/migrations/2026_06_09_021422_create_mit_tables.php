<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maba', function (Blueprint $table) {
            $table->id('maba_id');
            $table->string('nama');
            $table->string('nrp')->unique();
            $table->string('password');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('warga', function (Blueprint $table) {
            $table->id('warga_id');
            $table->string('nama');
            $table->string('nrp')->unique();
            $table->year('angkatan');
            $table->string('password');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('mit_week', function (Blueprint $table) {
            $table->id('week_id');
            $table->unsignedInteger('week_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['upcoming', 'active', 'completed'])->default('upcoming');
            $table->enum('availability_input_status', ['open', 'closed'])->default('closed');
            $table->timestamps();
        });

        Schema::create('kelompok_warga', function (Blueprint $table) {
            $table->id('kelompok_warga_id');

            $table->unsignedInteger('kode_kelompok')->unique();

            $table->text('rules')->nullable();

            $table->enum('status', ['draft', 'final'])->default('draft');

            $table->timestamps();
        });

        Schema::create('kelompok_warga_member', function (Blueprint $table) {
            $table->id('member_id');

            $table->foreignId('kelompok_warga_id')
                ->constrained('kelompok_warga', 'kelompok_warga_id')
                ->cascadeOnDelete();

            $table->foreignId('warga_id')
                ->constrained('warga', 'warga_id')
                ->cascadeOnDelete();

            $table->boolean('is_perwakilan')->default(false);

            $table->string('nomor_wa')->nullable();

            $table->timestamps();

            $table->unique(['kelompok_warga_id', 'warga_id']);

            $table->unique('warga_id');

            $table->index(['kelompok_warga_id', 'is_perwakilan']);
        });

        Schema::create('weekly_availability', function (Blueprint $table) {
            $table->id('availability_id');
            $table->foreignId('week_id')->constrained('mit_week', 'week_id')->cascadeOnDelete();
            $table->foreignId('kelompok_warga_id')->constrained('kelompok_warga', 'kelompok_warga_id')->cascadeOnDelete();
            $table->boolean('is_available')->default(true);
            $table->unsignedTinyInteger('session_mode')->default(4);
            $table->unsignedTinyInteger('session_count')->default(3);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['week_id', 'kelompok_warga_id']);
        });

        Schema::create('booking', function (Blueprint $table) {
            $table->id('booking_id');
            $table->foreignId('week_id')->constrained('mit_week', 'week_id')->cascadeOnDelete();
            $table->foreignId('kelompok_warga_id')->constrained('kelompok_warga', 'kelompok_warga_id')->cascadeOnDelete();
            $table->foreignId('created_by_maba_id')->constrained('maba', 'maba_id')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'cancelled', 'completed'])->default('pending');
            $table->dateTime('final_schedule')->nullable();
            $table->string('final_location')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->text('warga_notes')->nullable();
            $table->foreignId('decided_by_warga_id')->nullable()->constrained('warga', 'warga_id')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
            $table->index(['week_id', 'kelompok_warga_id', 'status']);
        });

        Schema::create('booking_participant', function (Blueprint $table) {
            $table->id('booking_participant_id');
            $table->foreignId('booking_id')->constrained('booking', 'booking_id')->cascadeOnDelete();
            $table->foreignId('maba_id')->constrained('maba', 'maba_id')->cascadeOnDelete();
            $table->enum('status', ['joined', 'left', 'present', 'absent', 'replaced'])->default('joined');
            $table->foreignId('replaced_by_maba_id')->nullable()->constrained('maba', 'maba_id')->nullOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->timestamps();
            $table->unique(['booking_id', 'maba_id']);
        });

        Schema::create('realisasi', function (Blueprint $table) {
            $table->id('realisasi_id');
            $table->foreignId('booking_id')->unique()->constrained('booking', 'booking_id')->cascadeOnDelete();
            $table->foreignId('week_id')->constrained('mit_week', 'week_id')->cascadeOnDelete();
            $table->foreignId('submitted_by_maba_id')->constrained('maba', 'maba_id')->cascadeOnDelete();
            $table->boolean('realisasi_is_meeting_held')->default(true);
            $table->boolean('is_warga_as_planned')->default(true);
            $table->text('absent_warga_notes')->nullable();
            $table->text('additional_warga_notes')->nullable();
            $table->text('general_notes')->nullable();
            $table->enum('status', ['pending', 'verified', 'revision', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by_admin_identifier')->nullable();
            $table->timestamps();
        });

        Schema::create('verification_result', function (Blueprint $table) {
            $table->id('verification_id');
            $table->foreignId('realisasi_id')->constrained('realisasi', 'realisasi_id')->cascadeOnDelete();
            $table->foreignId('maba_id')->constrained('maba', 'maba_id')->cascadeOnDelete();
            $table->foreignId('week_id')->constrained('mit_week', 'week_id')->cascadeOnDelete();
            $table->unsignedInteger('claimed_ttd_2022')->default(0);
            $table->unsignedInteger('claimed_ttd_2023')->default(0);
            $table->unsignedInteger('claimed_ttd_2024')->default(0);
            $table->unsignedInteger('verified_ttd_2022')->default(0);
            $table->unsignedInteger('verified_ttd_2023')->default(0);
            $table->unsignedInteger('verified_ttd_2024')->default(0);
            $table->enum('status', ['pending', 'verified', 'revision', 'rejected'])->default('pending');
            $table->text('admin_comment')->nullable();
            $table->string('verified_by_admin_identifier')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unique(['realisasi_id', 'maba_id']);
        });

        Schema::create('maba_kelompok_history', function (Blueprint $table) {
            $table->id('history_id');
            $table->foreignId('maba_id')->constrained('maba', 'maba_id')->cascadeOnDelete();
            $table->foreignId('kelompok_warga_id')->constrained('kelompok_warga', 'kelompok_warga_id')->cascadeOnDelete();
            $table->foreignId('week_id')->constrained('mit_week', 'week_id')->cascadeOnDelete();
            $table->foreignId('booking_id')->constrained('booking', 'booking_id')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();
            $table->unique(['maba_id', 'kelompok_warga_id']);
        });

        Schema::create('password_reset_request', function (Blueprint $table) {
            $table->id('reset_id');
            $table->enum('requester_type', ['maba', 'warga']);
            $table->unsignedBigInteger('requester_id');
            $table->string('nrp');
            $table->string('new_password');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('processed_by_admin_identifier')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_request');
        Schema::dropIfExists('maba_kelompok_history');
        Schema::dropIfExists('verification_result');
        Schema::dropIfExists('realisasi');
        Schema::dropIfExists('booking_participant');
        Schema::dropIfExists('booking');
        Schema::dropIfExists('weekly_availability');
        Schema::dropIfExists('kelompok_warga_member');
        Schema::dropIfExists('kelompok_warga');
        Schema::dropIfExists('mit_week');
        Schema::dropIfExists('warga');
        Schema::dropIfExists('maba');
    }
};
