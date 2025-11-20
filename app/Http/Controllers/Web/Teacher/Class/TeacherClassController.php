<?php

namespace App\Http\Controllers\Web\Teacher\Class;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\ClassModel;
use Inertia\Inertia;

class TeacherClassController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $query = $teacher->classes()
            ->select('classes.*')
            ->where('classes.status', 'active')
            ->with(['teachers.user', 'subjects', 'enrollments.student.user']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('classes.name', 'like', "%{$search}%")
                  ->orWhere('classes.code', 'like', "%{$search}%");
        }

        if ($request->has('academic_year')) {
            $query->where('classes.academic_year', $request->academic_year);
        }

        $classes = $query->latest('classes.created_at')->paginate(15);

        return Inertia::render('Teacher/Class/Index', [
            'classes' => $classes,
            'filters' => $request->only(['search', 'academic_year']),
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

        $class = $teacher->classes()
            ->select('classes.*')
            ->where('classes.id', $id)
            ->where('classes.status', 'active')
            ->with(['teachers.user', 'subjects', 'enrollments.student.user'])
            ->firstOrFail();

        // Get statistics
        $stats = [
            'total_students' => $class->enrollments()->where('status', 'active')->count(),
            'total_subjects' => $class->subjects()->count(),
            'total_teachers' => $class->teachers()->count(),
        ];

        return Inertia::render('Teacher/Class/Show', [
            'classItem' => $class,
            'stats' => $stats,
        ]);
    }
}
