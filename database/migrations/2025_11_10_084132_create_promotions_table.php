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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('from_class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('to_class_id')->constrained('classes')->onDelete('cascade');
            $table->string('from_academic_year');
            $table->string('to_academic_year');
            $table->date('promotion_date');
            $table->enum('promotion_type', ['automatic', 'manual', 'conditional', 'repeated'])->default('manual');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('promoted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('criteria_met')->nullable(); // Store promotion criteria (grades, attendance, etc.)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
