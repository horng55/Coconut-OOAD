<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create the pivot table
        Schema::create('class_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_model_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure a subject can only be assigned to a class once
            $table->unique(['class_model_id', 'subject_id']);
        });

        // Migrate existing subject_id data to the pivot table
        DB::table('classes')->whereNotNull('subject_id')->get()->each(function ($class) {
            DB::table('class_subject')->insert([
                'class_model_id' => $class->id,
                'subject_id' => $class->subject_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // Drop the subject_id column from classes table
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the subject_id column
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('code')->constrained('subjects')->onDelete('set null');
        });

        // Migrate data back from pivot table (take first subject if multiple)
        DB::table('class_subject')->get()->groupBy('class_model_id')->each(function ($subjects, $classId) {
            $firstSubject = $subjects->first();
            if ($firstSubject) {
                DB::table('classes')
                    ->where('id', $classId)
                    ->update(['subject_id' => $firstSubject->subject_id]);
            }
        });

        // Drop the pivot table
        Schema::dropIfExists('class_subject');
    }
};
