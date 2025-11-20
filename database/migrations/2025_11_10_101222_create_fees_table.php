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
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('fee_type', ['tuition', 'library', 'sports', 'exam', 'transport', 'hostel', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('cascade');
            $table->string('academic_year');
            $table->date('due_date')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurring_period', ['monthly', 'quarterly', 'semester', 'yearly'])->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
