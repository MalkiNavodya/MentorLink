<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentorship_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('mentor_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('message')->nullable();

            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentorship_requests');
    }
};