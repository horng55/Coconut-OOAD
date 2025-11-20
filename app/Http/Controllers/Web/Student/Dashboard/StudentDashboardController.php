<?php

namespace App\Http\Controllers\Web\Student\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Assessment;
use Inertia\Inertia;

class StudentDashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $student = Student::where('user_id', $user->id)
                ->with(['user', 'enrollments.classModel.teachers.user', 'grades.classModel'])
                ->first();

            if (!$student) {
                Auth::logout();
                return redirect()->route('student.login')
                    ->withErrors(['email' => 'Student record not found.']);
            }

            // Get enrolled classes
            $enrollments = $student->enrollments()
                ->where('status', 'active')
                ->with('classModel.teachers.user')
                ->latest()
                ->get();

            // Get recent grades (last 10)
            $recentGrades = $student->grades()
                ->with(['classModel', 'assessment'])
                ->latest('created_at')
                ->limit(10)
                ->get();

            // Get assignments for student's classes
            $classIds = $enrollments->pluck('class_id')->toArray();
            $assignments = Assessment::whereIn('class_id', $classIds)
                ->latest('created_at')
                ->limit(10)
                ->get();

            // Calculate statistics
            $stats = [
                'total_classes' => $enrollments->count(),
                'total_grades' => $student->grades()->count(),
                'total_assignments' => Assessment::whereIn('class_id', $classIds)->count(),
                'average_grade' => $student->grades()->avg('percentage') ? round($student->grades()->avg('percentage'), 1) : 0,
            ];

            \Log::info('Student Dashboard - Enrollments Count: ' . $enrollments->count());
            \Log::info('Student Dashboard - Stats: ' . json_encode($stats));

            return Inertia::render('Student/Dashboard', [
                'student' => $student,
                'enrollments' => $enrollments,
                'recentGrades' => $recentGrades,
                'assignments' => $assignments,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            \Log::error('Student Dashboard error: ' . $e->getMessage());
            
            return Inertia::render('Student/Dashboard', [
                'student' => null,
                'enrollments' => [],
                'recentGrades' => [],
                'assignments' => [],
                'stats' => [
                    'total_classes' => 0,
                    'total_grades' => 0,
                    'total_assignments' => 0,
                    'average_grade' => 0,
                ],
            ]);
        }
    }
}
