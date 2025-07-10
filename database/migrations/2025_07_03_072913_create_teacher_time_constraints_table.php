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
        Schema::create('teacher_time_constraints', function (Blueprint $table) {
            $table->id();

            // Dosen mana yang dibatasi
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('day_id')->constrained('days')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');

            $table->timestamps();

            // Mencegah batasan ganda untuk dosen di slot yang sama
            $table->unique(['teacher_id', 'day_id', 'time_slot_id'], 'teacher_day_time_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_time_constraints');
    }
};
