<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini menghubungkan activities dan teachers
        Schema::create('activity_teacher', function (Blueprint $table) {
            $table->foreignId('activity_id')->constrained('activities')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');

            // Primary key untuk mencegah entri duplikat
            $table->primary(['activity_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_teacher');
    }
};
