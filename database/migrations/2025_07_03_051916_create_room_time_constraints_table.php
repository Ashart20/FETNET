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
        Schema::create('room_time_constraints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_ruangan_id')->constrained('master_ruangans')->onDelete('cascade');
            $table->foreignId('day_id')->constrained('days')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');
            $table->timestamps();
            // Mencegah batasan ganda untuk slot yang sama
            $table->unique(['master_ruangan_id', 'day_id', 'time_slot_id'], 'room_day_time_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_time_constraints');
    }
};
