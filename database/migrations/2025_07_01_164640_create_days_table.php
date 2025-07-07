<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Contoh: Senin, Selasa
            // PERBAIKAN: Hapus timestamps karena ini data statis.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
