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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->decimal('quiz_stars', 3, 2)->default(0.0)->after('price');
            $table->integer('no_of_raters')->default(0)->after('quiz_stars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('quiz_stars');
            $table->dropColumn('no_of_raters');
        });
    }
};
