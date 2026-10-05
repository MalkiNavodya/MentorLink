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
        Schema::create('user_skills', function (Blueprint $table) {
            $table->id('user_skill_id');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('skill_id');

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('skill_id')
                ->references('skill_id')
                ->on('skills')
                ->cascadeOnDelete();

            $table->unique(['user_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_skills');
    }
};