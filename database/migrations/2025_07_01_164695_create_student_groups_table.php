<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_groups', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelompok'); // Contoh: "PTE 2023" (Year), "Kelas A" (Group), "Kelompok 1" (Subgroup)
            $table->string('kode_kelompok')->nullable();
            $table->string('angkatan')->nullable();
            $table->integer('jumlah_mahasiswa')->nullable();

            $table->foreignId('prodi_id')->constrained('prodis')->onDelete('cascade');


            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('student_groups')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['nama_kelompok', 'parent_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_groups');
    }
};
