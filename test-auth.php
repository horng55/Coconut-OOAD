<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

echo "=== Testing Authentication Data ===\n\n";

// Check teacher
$teacherEmail = 'teacher@schoolme.com';
$teacherUser = User::where('email', $teacherEmail)->first();
if ($teacherUser) {
    echo "✓ Teacher user found: {$teacherUser->name} (ID: {$teacherUser->id})\n";
    echo "  Status: {$teacherUser->status}\n";
    echo "  Password check: " . (Hash::check('password123', $teacherUser->password) ? "VALID" : "INVALID") . "\n";
    
    $teacher = Teacher::where('user_id', $teacherUser->id)->first();
    if ($teacher) {
        echo "  ✓ Teacher record found (ID: {$teacher->id})\n";
        echo "    Status: {$teacher->status}\n";
        echo "    Employee ID: {$teacher->employee_id}\n";
    } else {
        echo "  ✗ Teacher record NOT FOUND\n";
    }
} else {
    echo "✗ Teacher user NOT FOUND\n";
}

echo "\n";

// Check student
$studentEmail = 'student@schoolme.com';
$studentUser = User::where('email', $studentEmail)->first();
if ($studentUser) {
    echo "✓ Student user found: {$studentUser->name} (ID: {$studentUser->id})\n";
    echo "  Status: {$studentUser->status}\n";
    echo "  Password check: " . (Hash::check('password123', $studentUser->password) ? "VALID" : "INVALID") . "\n";
    
    $student = Student::where('user_id', $studentUser->id)->first();
    if ($student) {
        echo "  ✓ Student record found (ID: {$student->id})\n";
        echo "    Status: {$student->status}\n";
        echo "    Student ID: {$student->student_id}\n";
    } else {
        echo "  ✗ Student record NOT FOUND\n";
    }
} else {
    echo "✗ Student user NOT FOUND\n";
}

echo "\n=== Test Complete ===\n";
