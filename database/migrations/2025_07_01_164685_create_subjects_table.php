<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('nama_matkul');
            $table->string('kode_matkul');
            $table->integer('sks');
            $table->text('comments')->nullable();

            $table->foreignId('prodi_id')
                ->constrained('prodis')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['nama_matkul', 'prodi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
