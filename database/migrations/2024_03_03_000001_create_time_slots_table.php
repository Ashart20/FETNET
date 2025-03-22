<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // create_time_slots_table.php
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('constraints')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('time_slots');
    }
};
