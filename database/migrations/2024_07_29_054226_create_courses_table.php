<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('level');
            $table->integer('no_of_raters')->default(0);
            $table->decimal('course_stars', 2, 1)->default(0);
            $table->foreignId('course_category')->constrained('categories')->onDelete('cascade');
            $table->foreignId('sub_category')->constrained('subcategories')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('language');
            $table->timestamp('last_updated')->useCurrent();
            $table->string('demo_video')->nullable();
            $table->decimal('price', 12, 2);
            $table->text('overview')->nullable();
            $table->text('outcome')->nullable();
            $table->text('requirements')->nullable();
            $table->integer('total_lessons')->nullable();
            $table->timestamps();
        });

        Schema::create('course_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('part_id')->constrained('course_parts')->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['video', 'pdf']);
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */


    public function down(): void
    {
        Schema::dropIfExists('course_materials');
        Schema::dropIfExists('course_parts');
        Schema::dropIfExists('courses');
    }
};
