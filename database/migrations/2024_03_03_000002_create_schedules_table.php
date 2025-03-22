<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up()
{
Schema::create('schedules', function (Blueprint $table) {
$table->id();

// Data langsung dari XML Activity
$table->string('activity_id')->unique();
$table->string('subject');
$table->string('teacher');
$table->string('students_group')->nullable();
$table->integer('duration'); // Dalam jam
$table->string('constraints')->nullable();

// Relasi ke tabel lain
$table->foreignId('room_id')->constrained()->onDelete('cascade');
$table->foreignId('time_slot_id')->constrained()->onDelete('cascade');

$table->timestamps();
});
}

public function down()
{
Schema::dropIfExists('schedules');
}
};
