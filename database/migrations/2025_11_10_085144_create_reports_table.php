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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->enum('report_type', [
                'student_performance',
                'class_performance',
                'attendance',
                'grade_distribution',
                'teacher_workload',
                'enrollment',
                'custom'
            ]);
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('parameters')->nullable(); // Store report filters/parameters
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('file_path')->nullable(); // Path to generated PDF/Excel file
            $table->enum('status', ['pending', 'generating', 'completed', 'failed'])->default('pending');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
