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
        Schema::create('user_actions', function (Blueprint $table) {
            $table->id();
            $table->string('user_username')->nullable();
            $table->unsignedBigInteger('actionable_id')->nullable();
            $table->string('actionable_type')->nullable();
            $table->string('action_type'); // 'login', 'login_failed', 'logout'
            $table->string('portal')->nullable(); // 'admin', 'teacher', 'student', 'parent'
            $table->string('ip')->nullable();
            $table->json('metadata')->nullable(); // user_agent, guard, etc.
            $table->timestamps();

            $table->index(['action_type', 'created_at']);
            $table->index(['user_username', 'created_at']);
            $table->index(['portal', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_actions');
    }
};
