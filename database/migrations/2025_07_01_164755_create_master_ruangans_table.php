<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ruangan');
            $table->string('kode_ruangan')->unique();


            $table->foreignId('building_id')->nullable()->constrained('buildings')->onDelete('set null');

            $table->string('lantai');
            $table->integer('kapasitas');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Menjadikan nama ruangan unik dalam satu gedung
            $table->unique(['nama_ruangan', 'building_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_ruangans');
    }
};
