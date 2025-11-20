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
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'card', 'online', 'cheque', 'other'])->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('receipt_number')->nullable()->unique();
            $table->enum('status', ['pending', 'paid', 'partial', 'overdue', 'waived'])->default('pending');
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->string('academic_year');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'status']);
            $table->index(['fee_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
