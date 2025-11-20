<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('email', 'admin@schoolme.com')->first();
echo "Email: " . $user->email . PHP_EOL;
echo "Username: " . $user->username . PHP_EOL;
echo "Type: " . $user->type . PHP_EOL;
echo "Status: " . $user->status . PHP_EOL;
echo "Password Check (password123): " . (\Illuminate\Support\Facades\Hash::check('password123', $user->password) ? 'CORRECT' : 'WRONG') . PHP_EOL;

echo PHP_EOL . "Teacher:" . PHP_EOL;
$teacher = \App\Models\User::where('email', 'teacher@schoolme.com')->first();
echo "Email: " . $teacher->email . PHP_EOL;
echo "Username: " . $teacher->username . PHP_EOL;
echo "Type: " . $teacher->type . PHP_EOL;
echo "Password Check: " . (\Illuminate\Support\Facades\Hash::check('password123', $teacher->password) ? 'CORRECT' : 'WRONG') . PHP_EOL;

echo PHP_EOL . "Student:" . PHP_EOL;
$student = \App\Models\User::where('email', 'student@schoolme.com')->first();
echo "Email: " . $student->email . PHP_EOL;
echo "Username: " . $student->username . PHP_EOL;
echo "Type: " . $student->type . PHP_EOL;
echo "Password Check: " . (\Illuminate\Support\Facades\Hash::check('password123', $student->password) ? 'CORRECT' : 'WRONG') . PHP_EOL;
