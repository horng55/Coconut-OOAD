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
        // First, create some default subjects from existing class subjects
        $existingSubjects = DB::table('classes')
            ->whereNotNull('subject')
            ->where('subject', '!=', '')
            ->distinct()
            ->pluck('subject')
            ->filter();

        foreach ($existingSubjects as $subjectName) {
            if (!DB::table('subjects')->where('name', $subjectName)->exists()) {
                $code = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $subjectName), 0, 10));
                $counter = 1;
                while (DB::table('subjects')->where('code', $code)->exists()) {
                    $code = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $subjectName), 0, 8)) . $counter;
                    $counter++;
                }
                
                DB::table('subjects')->insert([
                    'name' => $subjectName,
                    'code' => $code,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Add subject_id column
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('code')->constrained('subjects')->onDelete('set null');
        });

        // Migrate existing subject data to subject_id
        DB::table('classes')->whereNotNull('subject')->get()->each(function ($class) {
            $subject = DB::table('subjects')->where('name', $class->subject)->first();
            if ($subject) {
                DB::table('classes')
                    ->where('id', $class->id)
                    ->update(['subject_id' => $subject->id]);
            }
        });

        // Drop the subject column
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the subject column
        Schema::table('classes', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('code');
        });

        // Migrate data back from subject_id
        DB::table('classes')->whereNotNull('subject_id')->get()->each(function ($class) {
            $subject = DB::table('subjects')->where('id', $class->subject_id)->first();
            if ($subject) {
                DB::table('classes')
                    ->where('id', $class->id)
                    ->update(['subject' => $subject->name]);
            }
        });

        // Drop the subject_id column
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });
    }
};
