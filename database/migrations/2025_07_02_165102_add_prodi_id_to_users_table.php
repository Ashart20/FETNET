<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom ini akan ditambahkan setelah kolom 'id' (atau sesuaikan jika perlu)
            $table->foreignId('prodi_id')->nullable()->after('id')->constrained('prodis')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // PERBAIKAN: Tambahkan logika untuk membatalkan migrasi
            $table->dropForeign(['prodi_id']);
            $table->dropColumn('prodi_id');
        });
    }
};
