<?php

namespace App\Http\Controllers\Web\Teacher\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Assessment;
use Inertia\Inertia;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $teacher = Teacher::where('user_id', $user->id)
                ->with(['user', 'classes.enrollments.student.user', 'classes.teachers.user', 'classes.subjects'])
                ->first();

            if (!$teacher) {
                Auth::logout();
                return redirect()->route('teacher.login')
                    ->withErrors(['email' => 'Teacher record not found.']);
            }

            // Get assigned classes
            $classes = $teacher->classes()
                ->select('classes.*')
                ->where('classes.status', 'active')
                ->with(['enrollments.student.user', 'teachers.user', 'subjects'])
                ->latest('classes.created_at')
                ->get();

            \Log::info('Teacher Dashboard - Classes Count: ' . $classes->count());

            // Get all students in teacher's classes
            $classIds = $classes->pluck('id')->toArray();
            
            \Log::info('Teacher Dashboard - Class IDs: ' . json_encode($classIds));

            $totalStudents = count($classIds) > 0 
                ? Enrollment::whereIn('class_id', $classIds)
                    ->where('status', 'active')
                    ->distinct('student_id')
                    ->count('student_id')
                : 0;

            // Get recent grades for teacher's classes (last 15)
            $recentGrades = count($classIds) > 0
                ? Grade::whereIn('class_id', $classIds)
                    ->with(['student.user', 'classModel'])
                    ->latest('created_at')
                    ->limit(15)
                    ->get()
                : collect([]);

            // Get assignments for teacher's classes
            $totalAssignments = count($classIds) > 0
                ? Assessment::whereIn('class_id', $classIds)->count()
                : 0;

            // Calculate statistics
            $stats = [
                'total_classes' => $classes->count(),
                'total_students' => $totalStudents,
                'total_assignments' => $totalAssignments,
                'total_grades' => count($classIds) > 0 ? Grade::whereIn('class_id', $classIds)->count() : 0,
            ];

            \Log::info('Teacher Dashboard - Stats: ' . json_encode($stats));
            \Log::info('Teacher Dashboard - Teacher Name: ' . ($teacher->user->full_name ?? 'NULL'));
            \Log::info('Teacher Dashboard - Classes Names: ' . $classes->pluck('name')->implode(', '));

            return Inertia::render('Teacher/Dashboard', [
                'teacher' => $teacher,
                'classes' => $classes,
                'recentGrades' => $recentGrades,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            \Log::error('Teacher Dashboard error: ' . $e->getMessage());
            
            return Inertia::render('Teacher/Dashboard', [
                'teacher' => null,
                'classes' => [],
                'recentGrades' => [],
                'stats' => [
                    'total_classes' => 0,
                    'total_students' => 0,
                    'total_assignments' => 0,
                    'total_grades' => 0,
                ],
            ]);
        }
    }
}
