<?php

namespace App\Http\Controllers\Web\Student\Class;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Enrollment;
use Inertia\Inertia;

class StudentClassController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.login')
                ->withErrors(['email' => 'Student record not found.']);
        }

        $query = Enrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->with(['classModel.teachers.user', 'classModel.subjects']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('classModel', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('academic_year')) {
            $query->whereHas('classModel', function ($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }

        $enrollments = $query->latest()->paginate(15);

        return Inertia::render('Student/Class/Index', [
            'enrollments' => $enrollments,
            'filters' => $request->only(['search', 'academic_year']),
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.login')
                ->withErrors(['email' => 'Student record not found.']);
        }

        $enrollment = Enrollment::where('id', $id)
            ->where('student_id', $student->id)
            ->where('status', 'active')
            ->with(['classModel.teachers.user', 'classModel.subjects', 'classModel.enrollments.student.user'])
            ->firstOrFail();

        return Inertia::render('Student/Class/Show', [
            'enrollment' => $enrollment,
        ]);
    }
}
