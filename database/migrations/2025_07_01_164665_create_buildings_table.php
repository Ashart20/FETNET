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
        // database/migrations/xxxx_create_buildings_table.php
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Contoh: "Gedung A", "Gedung B"
            $table->string('code')->unique(); // Contoh: "A", "B"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
