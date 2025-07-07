<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // Cukup simpan referensi ke 'activity', karena data subjek dan grup
            // sudah ada di dalam activity tersebut. Ini membuat skema lebih bersih.
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');

            // --- Kolom Hasil Penjadwalan ---
            $table->foreignId('room_id')->constrained('master_ruangans')->onDelete('cascade');
            $table->foreignId('day_id')->constrained('days')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
