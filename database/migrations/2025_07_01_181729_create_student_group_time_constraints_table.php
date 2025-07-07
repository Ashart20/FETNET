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
        Schema::create('student_group_time_constraints', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel-tabel master
            $table->foreignId('student_group_id')->constrained('student_groups')->onDelete('cascade');
            $table->foreignId('day_id')->constrained('days')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');

            $table->timestamps();

            // Mencegah batasan ganda untuk kelompok di slot waktu yang sama
            $table->unique(['student_group_id', 'day_id', 'time_slot_id'], 'student_group_day_time_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_group_time_constraints');
    }
};
