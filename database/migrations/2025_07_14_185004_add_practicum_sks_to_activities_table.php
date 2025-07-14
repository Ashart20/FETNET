<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // dalam file ...add_practicum_sks_to_activities_table.php
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Menambahkan kolom untuk menyimpan SKS praktikum tambahan
            $table->unsignedTinyInteger('practicum_sks')->nullable()->default(null)->after('duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            //
        });
    }
};
