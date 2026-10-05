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
        Schema::create('mentor_profiles', function (Blueprint $table) {
            $table->id('mentor_profile_id');

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('bio')->nullable();

            $table->string('university', 150)->nullable();

            $table->string('degree', 150)->nullable();

            $table->unsignedTinyInteger('academic_year')->nullable();

            $table->string('availability', 100)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_profiles');
    }
};