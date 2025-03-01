<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('course'); // Mata Kuliah
            $table->string('lecturer'); // Nama Dosen
            $table->string('room'); // Ruangan
            $table->string('time_slot'); // Waktu Kuliah
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};


