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
        Schema::table('schedules', function (Blueprint $table) {
            // Tambahkan kolom boolean untuk status aktif, defaultnya false (tidak aktif)
            // Anda bisa sesuaikan posisi 'after()' jika perlu
            $table->boolean('is_active')->default(false)->after('room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
