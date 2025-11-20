<?php

namespace App\Http\Controllers\Web\Admin\Grade;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $query = Grade::with('student.user', 'classModel', 'gradedBy');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $grades = $query->latest('assessment_date')->paginate(15);

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Grade/Index', [
            'grades' => $grades,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'student_id']),
        ]);
    }

    public function create(Request $request)
    {
        $classId = $request->class_id;
        $studentId = $request->student_id;

        $class = $classId ? ClassModel::find($classId) : null;
        $student = $studentId ? Student::with('user')->find($studentId) : null;

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        $students = collect();
        if ($classId && $class) {
            $students = $class->enrollments()
                ->where('status', 'active')
                ->with('student.user')
                ->get()
                ->map(function ($enrollment) {
                    return [
                        'id' => $enrollment->student->id,
                        'name' => $enrollment->student->user->full_name,
                        'student_id' => $enrollment->student->student_id,
                    ];
                });
        }

        $assessments = [];
        if ($classId) {
            $assessments = Assessment::where('class_id', $classId)
                ->where('status', 'active')
                ->get(['id', 'assessment_name', 'assessment_type', 'max_score', 'assessment_date'])
                ->toArray();
        }

        return Inertia::render('Admin/Grade/Create', [
            'classes' => $classes,
            'students' => $students,
            'selectedClass' => $class,
            'selectedStudent' => $student,
            'assessments' => $assessments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'assessment_id' => 'required|exists:assessments,id',
            'score' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        // Get assessment details
        $assessment = Assessment::findOrFail($validated['assessment_id']);
        
        // Validate score doesn't exceed maximum score
        if ($validated['score'] > $assessment->max_score) {
            return redirect()->back()
                ->withErrors(['score' => "Score cannot exceed the maximum score of {$assessment->max_score}."]);
        }
        
        $percentage = ($validated['score'] / $assessment->max_score) * 100;
        $letterGrade = $this->calculateLetterGrade($percentage);

        Grade::create([
            'student_id' => $validated['student_id'],
            'class_id' => $validated['class_id'],
            'assessment_id' => $validated['assessment_id'],
            'assessment_type' => $assessment->assessment_type,
            'assessment_name' => $assessment->assessment_name,
            'score' => $validated['score'],
            'max_score' => $assessment->max_score,
            'percentage' => round($percentage, 2),
            'letter_grade' => $letterGrade,
            'assessment_date' => $assessment->assessment_date,
            'comments' => $validated['comments'] ?? null,
            'graded_by' => Auth::id(),
        ]);

        return redirect()->route('admin.grades.index')
            ->with('success', 'Grade recorded successfully.');
    }

    public function show($id)
    {
        $grade = Grade::with('student.user', 'classModel', 'gradedBy')
            ->findOrFail($id);
        return Inertia::render('Admin/Grade/Show', [
            'grade' => $grade,
        ]);
    }

    public function edit($id)
    {
        $grade = Grade::with('student.user', 'classModel', 'gradedBy', 'assessment')->findOrFail($id);
        
        $assessments = [];
        if ($grade->class_id) {
            $assessments = Assessment::where('class_id', $grade->class_id)
                ->where('status', 'active')
                ->get(['id', 'assessment_name', 'assessment_type', 'max_score', 'assessment_date'])
                ->toArray();
        }
        
        return Inertia::render('Admin/Grade/Edit', [
            'grade' => $grade,
            'assessments' => $assessments,
        ]);
    }

    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);

        $validated = $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'score' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        // Get assessment details
        $assessment = Assessment::findOrFail($validated['assessment_id']);
        
        // Validate score doesn't exceed maximum score
        if ($validated['score'] > $assessment->max_score) {
            return redirect()->back()
                ->withErrors(['score' => "Score cannot exceed the maximum score of {$assessment->max_score}."]);
        }
        
        $percentage = ($validated['score'] / $assessment->max_score) * 100;
        $letterGrade = $this->calculateLetterGrade($percentage);

        $grade->update([
            'assessment_id' => $validated['assessment_id'],
            'assessment_type' => $assessment->assessment_type,
            'assessment_name' => $assessment->assessment_name,
            'score' => $validated['score'],
            'max_score' => $assessment->max_score,
            'percentage' => round($percentage, 2),
            'letter_grade' => $letterGrade,
            'assessment_date' => $assessment->assessment_date,
            'comments' => $validated['comments'] ?? null,
            'graded_by' => Auth::id(),
        ]);

        return redirect()->route('admin.grades.index')
            ->with('success', 'Grade updated successfully.');
    }

    public function destroy($id)
    {
        $grade = Grade::findOrFail($id);
        $grade->delete();

        return redirect()->route('admin.grades.index')
            ->with('success', 'Grade deleted successfully.');
    }

    private function calculateLetterGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }
}
