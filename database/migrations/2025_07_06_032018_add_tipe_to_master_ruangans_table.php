<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('master_ruangans', function (Blueprint $table) {
            $table->string('tipe')->default('KELAS_TEORI')->after('kapasitas');
        });
    }
    public function down(): void {
        Schema::table('master_ruangans', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
