<?php

namespace App\Http\Controllers\Web\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Basic counts with error handling
            $totalTeachers = Teacher::count() ?? 0;
            $totalStudents = Student::count() ?? 0;
            $totalClasses = ClassModel::count() ?? 0;
            $totalSubjects = Subject::count() ?? 0;
            $totalAssignments = Assessment::count() ?? 0;
            $totalSubmissions = Grade::count() ?? 0;

            // Grade statistics with null check
            $averageGrade = Grade::avg('percentage');
            $averageGrade = $averageGrade ? round($averageGrade, 1) : 0;

            $stats = [
                'total_teachers' => $totalTeachers,
                'total_students' => $totalStudents,
                'total_classes' => $totalClasses,
                'total_subjects' => $totalSubjects,
                'total_assignments' => $totalAssignments,
                'total_submissions' => $totalSubmissions,
                'average_grade' => $averageGrade,
            ];

            return Inertia::render('Dashboard', [
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Dashboard error: ' . $e->getMessage());
            
            // Return dashboard with default values
            $stats = [
                'total_teachers' => 0,
                'total_students' => 0,
                'total_classes' => 0,
                'total_subjects' => 0,
                'total_assignments' => 0,
                'total_submissions' => 0,
                'average_grade' => 0,
            ];

            return Inertia::render('Dashboard', [
                'stats' => $stats,
            ]);
        }
    }
}
