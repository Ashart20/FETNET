<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // ...
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dosen'); // <-- UBAH INI
            $table->string('kode_dosen')->unique(); // <-- UBAH INI
            //$table->foreignId('prodi_id')->constrained('prodis')->onDelete('cascade');
            $table->timestamps();
        });
    }
// ...
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
