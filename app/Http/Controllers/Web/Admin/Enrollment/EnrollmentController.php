<?php

namespace App\Http\Controllers\Web\Admin\Enrollment;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::with('student.user', 'classModel.teachers.user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhereHas('classModel', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->latest()->paginate(15);

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Enrollment/Index', [
            'enrollments' => $enrollments,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'status']),
        ]);
    }

    public function create()
    {
        $students = Student::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                    'student_id' => $student->student_id,
                ];
            });

        $classes = ClassModel::where('status', 'active')
            ->with('teachers.user')
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'code' => $class->code,
                    'teachers' => $class->teachers->map(function ($teacher) {
                        return $teacher->user->full_name;
                    })->join(', '),
                    'current_enrollments' => $class->enrollments()->where('status', 'active')->count(),
                    'max_students' => $class->max_students,
                ];
            });

        return Inertia::render('Admin/Enrollment/Create', [
            'students' => $students,
            'classes' => $classes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'notes' => 'nullable|string',
        ]);

        // Check if already enrolled
        $existing = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_id', $validated['class_id'])
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return back()->withErrors(['class_id' => 'Student is already enrolled in this class.']);
        }

        // Check class capacity
        $class = ClassModel::findOrFail($validated['class_id']);
        $currentEnrollments = $class->enrollments()->where('status', 'active')->count();
        if ($currentEnrollments >= $class->max_students) {
            return back()->withErrors(['class_id' => 'Class has reached maximum capacity.']);
        }

        Enrollment::create([
            'student_id' => $validated['student_id'],
            'class_id' => $validated['class_id'],
            'enrollment_date' => now(),
            'status' => 'active',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Student enrolled successfully.');
    }

    public function show($id)
    {
        $enrollment = Enrollment::with('student.user', 'classModel.teachers.user', 'classModel')
            ->findOrFail($id);
        return Inertia::render('Admin/Enrollment/Show', [
            'enrollment' => $enrollment,
        ]);
    }

    public function edit($id)
    {
        $enrollment = Enrollment::with('student.user', 'classModel')->findOrFail($id);
        return Inertia::render('Admin/Enrollment/Edit', [
            'enrollment' => $enrollment,
        ]);
    }

    public function update(Request $request, $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,completed',
            'notes' => 'nullable|string',
        ]);

        $enrollment->update($validated);

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment updated successfully.');
    }

    public function destroy($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return redirect()->route('admin.enrollments.index')
            ->with('success', 'Enrollment deleted successfully.');
    }
}
