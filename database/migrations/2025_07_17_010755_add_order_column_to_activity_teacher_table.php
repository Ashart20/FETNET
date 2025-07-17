<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_teacher', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan urutan (0, 1, 2, dst.)
            $table->unsignedInteger('order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('activity_teacher', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
