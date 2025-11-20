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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('assessment_type'); // exam, quiz, assignment, project, etc.
            $table->string('assessment_name');
            $table->decimal('score', 5, 2);
            $table->decimal('max_score', 5, 2)->default(100);
            $table->decimal('percentage', 5, 2)->nullable();
            $table->string('letter_grade')->nullable(); // A, B, C, D, F
            $table->date('assessment_date');
            $table->text('comments')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
