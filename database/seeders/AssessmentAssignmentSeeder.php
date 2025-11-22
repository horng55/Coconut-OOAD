<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Assignment;
use App\Models\ClassModel;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssessmentAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@schoolme.com')->first();
        $teacher = Teacher::first();
        $class = ClassModel::first();

        if (!$admin || !$teacher || !$class) {
            $this->command->error('❌ Required data not found. Please run DatabaseSeeder first.');
            return;
        }

        // Create Assessments
        Assessment::create([
            'class_id' => $class->id,
            'assessment_name' => 'Midterm Exam - Algebra',
            'assessment_type' => 'mid_term',
            'description' => 'Comprehensive exam covering algebraic concepts including linear equations, quadratic functions, and polynomials.',
            'assessment_date' => now()->addDays(7)->format('Y-m-d'),
            'max_score' => 100,
            'created_by' => $admin->id,
            'status' => 'active',
        ]);

        Assessment::create([
            'class_id' => $class->id,
            'assessment_name' => 'Quiz - Trigonometry Basics',
            'assessment_type' => 'quiz',
            'description' => 'Short quiz on sine, cosine, and tangent functions.',
            'assessment_date' => now()->addDays(3)->format('Y-m-d'),
            'max_score' => 50,
            'created_by' => $admin->id,
            'status' => 'active',
        ]);

        Assessment::create([
            'class_id' => $class->id,
            'assessment_name' => 'Final Project - Mathematical Research',
            'assessment_type' => 'project',
            'description' => 'Research and present a mathematical concept of your choice with real-world applications.',
            'assessment_date' => now()->addDays(30)->format('Y-m-d'),
            'max_score' => 150,
            'created_by' => $admin->id,
            'status' => 'active',
        ]);

        // Create Assignments
        Assignment::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Linear Equations Worksheet',
            'description' => 'Complete exercises 1-20 from Chapter 3. Show all your work and explain your reasoning for word problems.',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'max_score' => 100,
            'status' => 'active',
        ]);

        Assignment::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Geometry Proofs Assignment',
            'description' => 'Solve 5 geometric proofs using the theorems discussed in class. Include diagrams and step-by-step explanations.',
            'due_date' => now()->addDays(10)->format('Y-m-d'),
            'max_score' => 75,
            'status' => 'active',
        ]);

        $this->command->info('✅ Created ' . Assessment::count() . ' assessments');
        $this->command->info('✅ Created ' . Assignment::count() . ' assignments');
    }
}
