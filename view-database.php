<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== USERS TABLE ===\n\n";
$users = \App\Models\User::all();
foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Name: {$user->first_name} {$user->last_name}\n";
    echo "Email: {$user->email}\n";
    echo "Username: {$user->username}\n";
    echo "Type: {$user->type}\n";
    echo "Status: {$user->status}\n";
    echo "---\n";
}

echo "\n=== CLASSES TABLE ===\n\n";
$classes = \App\Models\ClassModel::all();
foreach ($classes as $class) {
    echo "ID: {$class->id}\n";
    echo "Name: {$class->name}\n";
    echo "Code: {$class->code}\n";
    echo "Academic Year: {$class->academic_year}\n";
    echo "---\n";
}

echo "\n=== ENROLLMENTS TABLE ===\n\n";
$enrollments = \App\Models\Enrollment::with(['student.user', 'classModel'])->get();
foreach ($enrollments as $enrollment) {
    echo "Student: {$enrollment->student->user->first_name} {$enrollment->student->user->last_name}\n";
    echo "Class: {$enrollment->classModel->name}\n";
    echo "Status: {$enrollment->status}\n";
    echo "---\n";
}
