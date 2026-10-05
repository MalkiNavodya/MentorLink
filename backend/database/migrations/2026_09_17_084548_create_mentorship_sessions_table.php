<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentorship_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mentorship_request_id')
                ->constrained('mentorship_requests')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('mentor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->dateTime('scheduled_at');

            $table->unsignedInteger('duration_minutes')
                ->default(60);

            $table->string('meeting_link')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->enum('status', [
                'scheduled',
                'completed',
                'cancelled',
            ])->default('scheduled');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentorship_sessions');
    }
};