<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodi_teacher', function (Blueprint $table) {
            $table->foreignId('prodi_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained()->onDelete('cascade');
            $table->primary(['prodi_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodi_teacher');
    }
};
