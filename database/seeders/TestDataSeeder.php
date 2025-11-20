<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\Assessment;
use App\Models\Grade;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Get teacher user
        $teacherUser = User::where('email', 'teacher@schoolme.com')->first();
        $teacher = Teacher::where('user_id', $teacherUser->id)->first();

        // Get student user
        $studentUser = User::where('email', 'student@schoolme.com')->first();
        $student = Student::where('user_id', $studentUser->id)->first();

        // Get or create class
        $class = ClassModel::first();
        
        if (!$class) {
            $class = ClassModel::create([
                'name' => 'Class 10A',
                'section' => 'A',
                'academic_year' => '2024-2025',
                'status' => 'active'
            ]);
        }

        // Create subject if not exists
        $subject = Subject::first();
        if (!$subject) {
            $subject = Subject::create([
                'name' => 'Mathematics',
                'code' => 'MATH101',
                'description' => 'Basic Mathematics',
                'status' => 'active'
            ]);
        }

        // Assign teacher to class
        if ($teacher && $class) {
            $class->teachers()->syncWithoutDetaching([$teacher->id]);
            $class->subjects()->syncWithoutDetaching([$subject->id]);
        }

        // Enroll student in class
        if ($student && $class) {
            Enrollment::firstOrCreate([
                'student_id' => $student->id,
                'class_id' => $class->id
            ], [
                'enrollment_date' => now(),
                'status' => 'active'
            ]);
        }

        // Create some assessments
        if ($class && $teacher) {
            for ($i = 1; $i <= 3; $i++) {
                $assessment = Assessment::firstOrCreate([
                    'assessment_name' => "Assignment $i",
                    'class_id' => $class->id,
                ], [
                    'description' => "This is assignment $i for the class",
                    'assessment_type' => 'assignment',
                    'max_score' => 100,
                    'assessment_date' => now()->addDays($i * 7),
                    'status' => 'active',
                    'created_by' => $teacherUser->id
                ]);
            }
        }

        // Create some grades for student
        if ($student && $class) {
            $assessments = Assessment::where('class_id', $class->id)->get();
            foreach ($assessments->take(2) as $assessment) {
                Grade::firstOrCreate([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'assessment_id' => $assessment->id,
                ], [
                    'assessment_type' => $assessment->assessment_type,
                    'assessment_name' => $assessment->assessment_name,
                    'score' => rand(70, 95),
                    'max_score' => 100,
                    'percentage' => rand(70, 95),
                    'letter_grade' => 'A',
                    'comments' => 'Good work',
                    'graded_by' => $teacherUser->id,
                    'assessment_date' => $assessment->assessment_date
                ]);
            }
        }

        echo "Test data seeded successfully!\n";
        echo "Teacher has " . $teacher->classes()->count() . " class(es)\n";
        echo "Student has " . $student->enrollments()->count() . " enrollment(s)\n";
        echo "Total Assessments: " . Assessment::count() . "\n";
        echo "Total Grades: " . Grade::count() . "\n";
    }
}
