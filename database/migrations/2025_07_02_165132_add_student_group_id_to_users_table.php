<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom ini akan diisi jika perannya adalah mahasiswa
            $table->foreignId('student_group_id')->nullable()->after('prodi_id')->constrained('student_groups')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['student_group_id']);
            $table->dropColumn('student_group_id');
        });
    }
};
