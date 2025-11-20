<?php

namespace App\Http\Controllers\Web\Teacher\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Enrollment;
use App\Models\Student;
use Inertia\Inertia;

class TeacherStudentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        // Get teacher's class IDs
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();

        $query = Student::whereHas('enrollments', function ($q) use ($classIds) {
            $q->whereIn('class_id', $classIds)
              ->where('status', 'active');
        })
        ->with(['user', 'enrollments.classModel']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('student_id', 'like', "%{$search}%");
        }

        if ($request->has('class_id') && in_array($request->class_id, $classIds)) {
            $query->whereHas('enrollments', function ($q) use ($request) {
                $q->where('class_id', $request->class_id)
                  ->where('status', 'active');
            });
        }

        $students = $query->latest()->paginate(15);

        // Get classes for filter
        $classes = \App\Models\ClassModel::whereIn('id', $classIds)
            ->where('status', 'active')
            ->get(['id', 'name', 'code']);

        return Inertia::render('Teacher/Student/Index', [
            'students' => $students,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id']),
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        // Get teacher's class IDs
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();

        $student = Student::whereHas('enrollments', function ($q) use ($classIds) {
            $q->whereIn('class_id', $classIds)
              ->where('status', 'active');
        })
        ->with(['user', 'enrollments.classModel', 'grades.classModel', 'attendances.classModel'])
        ->findOrFail($id);

        // Get statistics
        $stats = [
            'total_classes' => $student->enrollments()->whereIn('class_id', $classIds)->where('status', 'active')->count(),
            'total_grades' => $student->grades()->whereIn('class_id', $classIds)->count(),
            'total_attendance' => $student->attendances()->whereIn('class_id', $classIds)->count(),
            'present_count' => $student->attendances()->whereIn('class_id', $classIds)->where('status', 'present')->count(),
        ];

        return Inertia::render('Teacher/Student/Show', [
            'student' => $student,
            'stats' => $stats,
        ]);
    }
}
