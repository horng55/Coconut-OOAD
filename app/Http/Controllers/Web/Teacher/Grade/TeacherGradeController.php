<?php

namespace App\Http\Controllers\Web\Teacher\Grade;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TeacherGradeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        // Get teacher's class IDs
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();

        $query = Grade::whereIn('class_id', $classIds)
            ->with('student.user', 'classModel', 'gradedBy');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id') && in_array($request->class_id, $classIds)) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $grades = $query->latest('assessment_date')->paginate(15);

        $classes = $teacher->classes()
            ->select('classes.id', 'classes.name', 'classes.code')
            ->where('classes.status', 'active')
            ->get();

        return Inertia::render('Teacher/Grade/Index', [
            'grades' => $grades,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'student_id']),
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $classId = $request->class_id;
        $studentId = $request->student_id;

        // Only allow access to teacher's classes
        $class = null;
        if ($classId) {
            $class = $teacher->classes()
                ->select('classes.*')
                ->where('classes.id', $classId)
                ->where('classes.status', 'active')
                ->first();
        }

        $student = $studentId ? Student::with('user')->find($studentId) : null;

        $classes = $teacher->classes()
            ->select('classes.id', 'classes.name', 'classes.code')
            ->where('classes.status', 'active')
            ->get();

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

        return Inertia::render('Teacher/Grade/Create', [
            'classes' => $classes,
            'students' => $students,
            'selectedClass' => $class,
            'selectedStudent' => $student,
            'assessments' => $assessments,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        // Verify the class belongs to this teacher
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id|in:' . implode(',', $classIds),
            'assessment_id' => 'required|exists:assessments,id',
            'score' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
        ]);

        // Get assessment details
        $assessment = Assessment::findOrFail($validated['assessment_id']);
        
        // Verify the assessment belongs to the selected class
        if ($assessment->class_id != $validated['class_id']) {
            return redirect()->back()
                ->withErrors(['assessment_id' => 'The selected assessment does not belong to the selected class.']);
        }
        
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

        return redirect()->route('teacher.grades.index')
            ->with('success', 'Grade recorded successfully.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        // Get teacher's class IDs
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();

        // Get the grade and verify it belongs to teacher's class
        $grade = Grade::whereIn('class_id', $classIds)
            ->with('student.user', 'classModel', 'gradedBy', 'assessment')
            ->findOrFail($id);

        $assessments = [];
        if ($grade->class_id) {
            $assessments = Assessment::where('class_id', $grade->class_id)
                ->where('status', 'active')
                ->get(['id', 'assessment_name', 'assessment_type', 'max_score', 'assessment_date'])
                ->toArray();
        }

        return Inertia::render('Teacher/Grade/Edit', [
            'grade' => $grade,
            'assessments' => $assessments,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        // Verify the grade belongs to this teacher's class
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();
        $grade = Grade::whereIn('class_id', $classIds)->findOrFail($id);

        $validated = $request->validate([
            'assessment_id' => 'nullable|exists:assessments,id',
            'assessment_type' => 'required|in:quiz,assignment,exam,project,participation,mid_term',
            'assessment_name' => 'required|string|max:255',
            'score' => 'required|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'assessment_date' => 'required|date',
            'comments' => 'nullable|string',
        ]);

        $percentage = ($validated['score'] / $validated['max_score']) * 100;
        $letterGrade = $this->calculateLetterGrade($percentage);

        $grade->update([
            'assessment_id' => $validated['assessment_id'] ?? null,
            'assessment_type' => $validated['assessment_type'],
            'assessment_name' => $validated['assessment_name'],
            'score' => $validated['score'],
            'max_score' => $validated['max_score'],
            'percentage' => round($percentage, 2),
            'letter_grade' => $letterGrade,
            'assessment_date' => $validated['assessment_date'],
            'comments' => $validated['comments'] ?? null,
            'graded_by' => Auth::id(),
        ]);

        return redirect()->route('teacher.grades.index')
            ->with('success', 'Grade updated successfully.');
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

