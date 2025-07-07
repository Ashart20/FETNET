<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_groups', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelompok'); // Contoh: "PTE 2023" (Year), "Kelas A" (Group), "Kelompok 1" (Subgroup)
            $table->string('kode_kelompok')->nullable(); // Dibuat nullable, karena tidak semua level hierarki butuh kode
            $table->string('angkatan')->nullable();
            $table->integer('jumlah_mahasiswa')->nullable();

            $table->foreignId('prodi_id')->constrained('prodis')->onDelete('cascade');

            // PERBAIKAN 1: Tambahkan parent_id untuk hierarki
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('student_groups') // Foreign key ke tabel itu sendiri
                ->onDelete('cascade'); // Jika parent dihapus, semua children ikut terhapus

            $table->timestamps();

            // PERBAIKAN 2: Pastikan nama kelompok unik di dalam parent yang sama
            // Mencegah ada 2 "Kelas A" di dalam "PTE 2023"
            $table->unique(['nama_kelompok', 'parent_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('student_groups');
    }
};
