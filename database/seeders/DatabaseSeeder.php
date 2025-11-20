<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Split full name into first and last name
     */
    private function splitName($fullName): array
    {
        $parts = explode(' ', trim($fullName), 2);
        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ];
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $adminName = $this->splitName('Admin User');
        $admin = User::create([
            'first_name' => $adminName['first_name'],
            'last_name' => $adminName['last_name'],
            'username' => 'admin',
            'email' => 'admin@schoolme.com',
            'type' => 'admin',
            'status' => 'active',
            'verified' => true,
            'password' => Hash::make('password123'),
        ]);

        // Create Teachers
        $teachers = [
            [
                'name' => 'John Smith',
                'username' => 'teacher',
                'email' => 'teacher@schoolme.com',
                'employee_id' => 'T001',
                'subject_specialization' => 'Mathematics',
                'qualification' => 'M.Sc. Mathematics',
                'hire_date' => '2020-01-15',
            ],
        ];

        $createdTeachers = [];
        foreach ($teachers as $teacherData) {
            $nameParts = $this->splitName($teacherData['name']);
            $user = User::create([
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
                'username' => $teacherData['username'],
                'email' => $teacherData['email'],
                'type' => 'teacher',
                'status' => 'active',
                'verified' => true,
                'password' => Hash::make('password123'),
                'gender' => 'male',
            ]);

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'employee_id' => $teacherData['employee_id'],
                'subject_specialization' => $teacherData['subject_specialization'],
                'qualification' => $teacherData['qualification'],
                'hire_date' => $teacherData['hire_date'],
                'status' => 'active',
            ]);

            $createdTeachers[] = $teacher;
        }

        // Create Students
        $students = [
            [
                'name' => 'Emma Williams',
                'username' => 'student',
                'email' => 'student@schoolme.com',
                'student_id' => 'S001',
                'admission_date' => '2023-09-01',
                'gender' => 'female',
            ],
        ];

        $createdStudents = [];
        foreach ($students as $studentData) {
            $nameParts = $this->splitName($studentData['name']);
            $user = User::create([
                'first_name' => $nameParts['first_name'],
                'last_name' => $nameParts['last_name'],
                'username' => $studentData['username'],
                'email' => $studentData['email'],
                'type' => 'student',
                'status' => 'active',
                'verified' => true,
                'password' => Hash::make('password123'),
                'gender' => $studentData['gender'],
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'parent_id' => null,
                'student_id' => $studentData['student_id'],
                'admission_date' => $studentData['admission_date'],
                'status' => 'active',
            ]);

            $createdStudents[] = $student;
        }

        // Create Classes
        $currentYear = date('Y');
        $academicYear = ($currentYear - 1) . '-' . $currentYear;

        $classes = [
            [
                'name' => 'Grade 10A - Mathematics',
                'code' => 'MATH-10A',
                'teacher_id' => $createdTeachers[0]->id,
                'description' => 'Advanced Mathematics for Grade 10',
                'academic_year' => $academicYear,
                'max_students' => 30,
            ],
        ];

        $createdClasses = [];
        foreach ($classes as $classData) {
            $class = ClassModel::create([
                'name' => $classData['name'],
                'code' => $classData['code'],
                'description' => $classData['description'],
                'academic_year' => $classData['academic_year'],
                'status' => 'active',
                'max_students' => $classData['max_students'],
            ]);

            // Attach teacher to class using the pivot table
            $class->teachers()->attach($classData['teacher_id']);

            $createdClasses[] = $class;
        }

        // Create Enrollments
        $enrollments = [
            ['student_id' => $createdStudents[0]->id, 'class_id' => $createdClasses[0]->id],
        ];

        foreach ($enrollments as $enrollmentData) {
            Enrollment::create([
                'student_id' => $enrollmentData['student_id'],
                'class_id' => $enrollmentData['class_id'],
                'enrollment_date' => now(),
                'status' => 'active',
            ]);
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📧 Admin Login: admin@schoolme.com / password123');
        $this->command->info('👨‍🏫 Teacher Login: teacher@schoolme.com / password123');
        $this->command->info('👨‍🎓 Student Login: student@schoolme.com / password123');
        $this->command->info('📚 Created ' . count($createdClasses) . ' class');
        $this->command->info('📝 Created ' . count($enrollments) . ' enrollment');
    }
}
