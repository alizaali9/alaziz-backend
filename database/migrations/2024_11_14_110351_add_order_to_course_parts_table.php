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
        Schema::table('course_parts', function (Blueprint $table) {
            $table->integer('order')->after('name');
        });
    }

    public function down()
    {
        Schema::table('course_parts', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }

};
