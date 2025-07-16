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
        // Mengubah tabel yang sudah ada
        Schema::table('activity_teacher', function (Blueprint $table) {
            // Ini akan menambahkan kolom created_at dan updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Logika untuk membatalkan migrasi
        Schema::table('activity_teacher', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
