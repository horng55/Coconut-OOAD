<?php

namespace App\Http\Controllers\Web\Student\Grade;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Grade;
use Inertia\Inertia;

class StudentGradeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.login')
                ->withErrors(['email' => 'Student record not found.']);
        }

        $query = Grade::where('student_id', $student->id)
            ->with(['classModel', 'assessment']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('classModel', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhereHas('assessment', function ($q) use ($search) {
                $q->where('assessment_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $grades = $query->latest('assessment_date')->paginate(15);

        // Get statistics
        $stats = [
            'total_grades' => Grade::where('student_id', $student->id)->count(),
            'average_percentage' => Grade::where('student_id', $student->id)->avg('percentage') ?? 0,
            'highest_grade' => Grade::where('student_id', $student->id)->max('percentage') ?? 0,
            'lowest_grade' => Grade::where('student_id', $student->id)->min('percentage') ?? 0,
        ];

        // Get classes for filter
        $classes = \App\Models\ClassModel::whereHas('enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id)->where('status', 'active');
        })->get(['id', 'name', 'code']);

        return Inertia::render('Student/Grade/Index', [
            'grades' => $grades,
            'classes' => $classes,
            'stats' => $stats,
            'filters' => $request->only(['search', 'class_id', 'academic_year']),
        ]);
    }
}
