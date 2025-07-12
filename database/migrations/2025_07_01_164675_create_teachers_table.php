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
            $table->string('nama_dosen');
            $table->string('kode_dosen')->unique();
            $table->string('title_depan', 50)->nullable();
            $table->string('title_belakang', 100)->nullable();
            $table->string('kode_univ')->nullable()->unique();
            $table->string('employee_id')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('nomor_hp')->nullable();
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
