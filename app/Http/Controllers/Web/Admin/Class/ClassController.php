<?php

namespace App\Http\Controllers\Web\Admin\Class;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassModel::with('teachers.user', 'subjects');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('subjects', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $classes = $query->latest()->paginate(15);

        return Inertia::render('Admin/Class/Index', [
            'classes' => $classes,
            'filters' => $request->only(['search', 'academic_year']),
        ]);
    }

    public function create()
    {
        $teachers = Teacher::with('user')->get()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->user->full_name,
                'employee_id' => $teacher->employee_id,
            ];
        });

        $subjects = Subject::where('status', 'active')->get(['id', 'name', 'code']);

        $currentYear = date('Y');
        $academicYear = ($currentYear - 1) . '-' . $currentYear;

        return Inertia::render('Admin/Class/Create', [
            'teachers' => $teachers,
            'subjects' => $subjects,
            'defaultAcademicYear' => $academicYear,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:classes,code',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id',
            'description' => 'nullable|string',
            'academic_year' => 'required|string|max:255',
            'max_students' => 'required|integer|min:1',
        ]);

        $class = ClassModel::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'academic_year' => $validated['academic_year'],
            'max_students' => $validated['max_students'],
            'status' => 'active',
        ]);

        // Sync subjects
        if (isset($validated['subject_ids'])) {
            $class->subjects()->sync($validated['subject_ids'] ?? []);
        }

        // Sync teachers
        if (!empty($validated['teacher_ids'])) {
            $class->teachers()->sync($validated['teacher_ids']);
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function show($id)
    {
        $class = ClassModel::with([
            'subjects',
            'teachers.user',
            'enrollments' => function ($query) {
                $query->where('status', 'active')->with('student.user');
            },
            'announcements'
        ])->findOrFail($id);
        
        return Inertia::render('Admin/Class/Show', [
            'classData' => $class,
        ]);
    }

    public function edit($id)
    {
        $class = ClassModel::with('teachers', 'subjects')->findOrFail($id);
        $teachers = Teacher::with('user')->get()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->user->full_name,
                'employee_id' => $teacher->employee_id,
            ];
        });

        $subjects = Subject::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Class/Edit', [
            'classData' => $class,
            'teachers' => $teachers,
            'subjects' => $subjects,
        ]);
    }

    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:classes,code,' . $id,
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id',
            'description' => 'nullable|string',
            'academic_year' => 'required|string|max:255',
            'max_students' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,completed',
        ]);

        $class->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'academic_year' => $validated['academic_year'],
            'max_students' => $validated['max_students'],
            'status' => $validated['status'],
        ]);

        // Sync subjects
        if (isset($validated['subject_ids'])) {
            $class->subjects()->sync($validated['subject_ids'] ?? []);
        }

        // Sync teachers
        if (isset($validated['teacher_ids'])) {
            $class->teachers()->sync($validated['teacher_ids'] ?? []);
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}
