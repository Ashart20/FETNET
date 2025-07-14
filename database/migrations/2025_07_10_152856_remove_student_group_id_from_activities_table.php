<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'student_group_id')) {

                try {
                    $table->dropForeign('activities_student_group_id_foreign');
                } catch (\Exception $e) {

                    if (str_contains($e->getMessage(), '1091') || str_contains($e->getMessage(), 'foreign key constraint') || str_contains($e->getMessage(), 'constraint fails')) {
                    } else {

                        throw $e;
                    }
                }

                $table->dropColumn('student_group_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (! Schema::hasColumn('activities', 'student_group_id')) {
                $table->foreignId('student_group_id')->nullable()->constrained('student_groups')->onDelete('cascade');
            }
        });
    }
};
