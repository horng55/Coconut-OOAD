<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assessment;
use App\Models\Grade;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class SyncAssessmentGrades extends Command
{
    protected $signature = 'assessment:sync-grades {assessment_id?}';
    protected $description = 'Create grade records for students enrolled in assessment classes';

    public function handle()
    {
        $assessmentId = $this->argument('assessment_id');

        if ($assessmentId) {
            $assessments = Assessment::where('id', $assessmentId)->get();
        } else {
            $assessments = Assessment::all();
        }

        $this->info('Starting grade synchronization...');

        foreach ($assessments as $assessment) {
            $this->info("Processing Assessment: {$assessment->assessment_name} (ID: {$assessment->id})");

            // Get all students enrolled in this assessment's class
            $enrolledStudents = Enrollment::where('class_id', $assessment->class_id)
                ->where('status', 'active')
                ->pluck('student_id');

            $this->info("Found {$enrolledStudents->count()} enrolled students");

            // Get existing grade records for this assessment
            $existingGrades = Grade::where('assessment_id', $assessment->id)
                ->pluck('student_id');

            // Find students without grade records
            $studentsNeedingGrades = $enrolledStudents->diff($existingGrades);

            if ($studentsNeedingGrades->isEmpty()) {
                $this->info("✓ All students already have grade records");
                continue;
            }

            $this->info("Creating grade records for {$studentsNeedingGrades->count()} students...");

            DB::beginTransaction();
            try {
                foreach ($studentsNeedingGrades as $studentId) {
                    Grade::create([
                        'student_id' => $studentId,
                        'class_id' => $assessment->class_id,
                        'assessment_id' => $assessment->id,
                        'assessment_type' => $assessment->assessment_type,
                        'assessment_name' => $assessment->assessment_name,
                        'assessment_date' => $assessment->assessment_date,
                        'max_score' => $assessment->max_score,
                        'score' => 0,
                        'graded_by' => $assessment->created_by,
                    ]);
                }

                DB::commit();
                $this->info("✓ Successfully created {$studentsNeedingGrades->count()} grade records");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("✗ Failed to create grades: {$e->getMessage()}");
            }
        }

        $this->info('Grade synchronization completed!');
    }
}
