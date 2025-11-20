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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Grade 10A", "Math 101"
            $table->string('code')->unique();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('academic_year');
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->integer('max_students')->default(30);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_models');
    }
};
