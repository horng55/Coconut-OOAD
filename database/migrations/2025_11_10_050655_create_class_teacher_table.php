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
        Schema::create('class_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_model_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure a teacher can only be assigned to a class once
            $table->unique(['class_model_id', 'teacher_id']);
        });

        // Migrate existing teacher_id data to the pivot table
        DB::table('classes')->whereNotNull('teacher_id')->get()->each(function ($class) {
            DB::table('class_teacher')->insert([
                'class_model_id' => $class->id,
                'teacher_id' => $class->teacher_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // Drop the teacher_id column from classes table
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the teacher_id column
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->after('code')->constrained('teachers')->onDelete('set null');
        });

        // Migrate data back from pivot table (take first teacher if multiple)
        DB::table('class_teacher')->get()->groupBy('class_model_id')->each(function ($teachers, $classId) {
            $firstTeacher = $teachers->first();
            if ($firstTeacher) {
                DB::table('classes')
                    ->where('id', $classId)
                    ->update(['teacher_id' => $firstTeacher->teacher_id]);
            }
        });

        // Drop the pivot table
        Schema::dropIfExists('class_teacher');
    }
};
