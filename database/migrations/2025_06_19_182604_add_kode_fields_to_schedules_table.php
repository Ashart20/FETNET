<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('kode_mk')->nullable()->after('activity_id');
            $table->string('kode_dosen')->nullable()->after('teacher');
            $table->string('kelas')->nullable()->after('teacher');
            $table->integer('sks')->nullable()->after('kelas');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['kode_mk', 'kode_dosen', 'kelas', 'sks']);
        });
    }
};
