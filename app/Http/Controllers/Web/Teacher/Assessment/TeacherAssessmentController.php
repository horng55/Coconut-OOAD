<?php

namespace App\Http\Controllers\Web\Teacher\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\AssessmentSubmission;
use App\Support\Service\FlashMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TeacherAssessmentController extends Controller
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

        $query = Assessment::whereIn('class_id', $classIds)
            ->with('classModel', 'createdBy');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('assessment_name', 'like', "%{$search}%")
                  ->orWhereHas('classModel', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
        }

        if ($request->has('class_id') && in_array($request->class_id, $classIds)) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('assessment_type')) {
            $query->where('assessment_type', $request->assessment_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $assessments = $query->latest('assessment_date')->paginate(15);

        $classes = $teacher->classes()
            ->select('classes.id', 'classes.name', 'classes.code')
            ->where('classes.status', 'active')
            ->get();

        return Inertia::render('Teacher/Assessment/Index', [
            'assessments' => $assessments,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'assessment_type', 'status']),
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

        // Only allow access to teacher's classes
        $class = null;
        $students = [];
        if ($classId) {
            $class = $teacher->classes()
                ->select('classes.*')
                ->where('classes.id', $classId)
                ->where('classes.status', 'active')
                ->first();

            // Get students enrolled in this class
            if ($class) {
                $students = \App\Models\Enrollment::where('class_id', $classId)
                    ->where('status', 'active')
                    ->with('student.user:id,first_name,last_name,email')
                    ->get()
                    ->map(function ($enrollment) {
                        return [
                            'id' => $enrollment->student->id,
                            'student_id' => $enrollment->student->student_id,
                            'name' => $enrollment->student->user->full_name,
                            'email' => $enrollment->student->user->email,
                        ];
                    });
            }
        }

        $classes = $teacher->classes()
            ->select('classes.id', 'classes.name', 'classes.code')
            ->where('classes.status', 'active')
            ->get();

        return Inertia::render('Teacher/Assessment/Create', [
            'classes' => $classes,
            'selectedClass' => $class,
            'students' => $students,
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

        \Log::info('Assessment Store Request', [
            'request_data' => $request->all(),
            'teacher_id' => $teacher->id,
        ]);

        // Verify the class belongs to this teacher
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();

        \Log::info('Teacher Class IDs', ['class_ids' => $classIds]);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id|in:' . implode(',', $classIds),
            'assessment_type' => 'required|in:quiz,assignment,exam,project,participation,mid_term',
            'assessment_name' => 'required|string|max:255',
            'score' => 'nullable|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'assessment_date' => 'required|date',
            'description' => 'nullable|string',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        \Log::info('Validated Data', ['validated' => $validated]);

        DB::beginTransaction();
        try {
            $assessment = Assessment::create([
                'class_id' => $validated['class_id'],
                'assessment_type' => $validated['assessment_type'],
                'assessment_name' => $validated['assessment_name'],
                'score' => $validated['score'] ?? null,
                'max_score' => $validated['max_score'],
                'assessment_date' => $validated['assessment_date'],
                'description' => $validated['description'] ?? null,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            // Create grade entries for selected students
            if (!empty($validated['student_ids'])) {
                foreach ($validated['student_ids'] as $studentId) {
                    Grade::create([
                        'student_id' => $studentId,
                        'class_id' => $validated['class_id'],
                        'assessment_id' => $assessment->id,
                        'assessment_type' => $validated['assessment_type'],
                        'assessment_name' => $validated['assessment_name'],
                        'max_score' => $validated['max_score'],
                        'assessment_date' => $validated['assessment_date'],
                        'score' => 0, // Default score, teacher can grade later
                        'graded_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            \Log::info('Assessment created successfully', ['assessment_id' => $assessment->id]);

            FlashMessage::success('Assessment created successfully and assigned to ' . count($validated['student_ids'] ?? []) . ' student(s).');
            
            return redirect()->route('teacher.assessments.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Assessment creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Failed to create assessment: ' . $e->getMessage()]);
        }
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

        // Get the assessment and verify it belongs to teacher's class
        $assessment = Assessment::whereIn('class_id', $classIds)
            ->with('classModel', 'createdBy')
            ->findOrFail($id);

        $classes = $teacher->classes()
            ->select('classes.id', 'classes.name', 'classes.code')
            ->where('classes.status', 'active')
            ->get();

        return Inertia::render('Teacher/Assessment/Edit', [
            'assessment' => $assessment,
            'classes' => $classes,
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

        // Verify the assessment belongs to this teacher's class
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();
        $assessment = Assessment::whereIn('class_id', $classIds)->findOrFail($id);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id|in:' . implode(',', $classIds),
            'assessment_type' => 'required|in:quiz,assignment,exam,project,participation,mid_term',
            'assessment_name' => 'required|string|max:255',
            'score' => 'nullable|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'assessment_date' => 'required|date',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $assessment->update([
            'class_id' => $validated['class_id'],
            'assessment_type' => $validated['assessment_type'],
            'assessment_name' => $validated['assessment_name'],
            'score' => $validated['score'] ?? null,
            'max_score' => $validated['max_score'],
            'assessment_date' => $validated['assessment_date'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        FlashMessage::success('Assessment updated successfully.');
        
        return redirect()->route('teacher.assessments.index');
    }

    public function show(Request $request, $id)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        // Verify the assessment belongs to this teacher's class
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();
        $assessment = Assessment::with(['classModel', 'createdBy'])
            ->whereIn('class_id', $classIds)
            ->findOrFail($id);

        // Get all students assigned to this assessment with their grades and submissions
        $studentsWithGrades = Grade::where('assessment_id', $id)
            ->with([
                'student.user',
                'gradedBy',
                'student.submissions' => function ($query) use ($id) {
                    $query->where('assessment_id', $id);
                }
            ])
            ->get()
            ->map(function ($grade) {
                $submission = $grade->student->submissions->first();
                return [
                    'id' => $grade->id,
                    'student_id' => $grade->student_id,
                    'student_name' => $grade->student->user->full_name ?? 'N/A',
                    'student_number' => $grade->student->student_id,
                    'student_email' => $grade->student->user->email ?? 'N/A',
                    'score' => $grade->score,
                    'feedback' => $grade->feedback,
                    'graded_by' => $grade->gradedBy ? $grade->gradedBy->full_name : null,
                    'updated_at' => $grade->updated_at,
                    'has_submission' => $submission !== null,
                    'submission' => $submission ? [
                        'id' => $submission->id,
                        'file_name' => $submission->file_name,
                        'file_path' => $submission->file_path,
                        'file_size' => $submission->file_size,
                        'comments' => $submission->comments,
                        'submitted_at' => $submission->submitted_at,
                    ] : null,
                ];
            });

        return Inertia::render('Teacher/Assessment/Show', [
            'assessment' => $assessment,
            'students' => $studentsWithGrades,
            'highlightSubmission' => $request->query('submission') ? (int)$request->query('submission') : null,
        ]);
    }

    public function updateGrade(Request $request, $id)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $request->validate([
            'score' => 'required|numeric|min:0',
            'feedback' => 'nullable|string|max:1000',
        ]);

        $grade = Grade::findOrFail($id);

        // Verify the grade's assessment belongs to this teacher's class
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();
        $assessment = Assessment::whereIn('class_id', $classIds)->findOrFail($grade->assessment_id);

        // Validate score doesn't exceed max_score
        if ($request->score > $assessment->max_score) {
            return redirect()->back()
                ->withErrors(['score' => "Score cannot exceed maximum score of {$assessment->max_score}"]);
        }

        $grade->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'graded_by' => Auth::id(),
        ]);

        FlashMessage::success('Grade updated successfully.');

        return redirect()->back();
    }

    public function downloadSubmission($submissionId)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $submission = AssessmentSubmission::with('assessment')->findOrFail($submissionId);

        // Verify the submission's assessment belongs to this teacher's class
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();
        if (!in_array($submission->assessment->class_id, $classIds)) {
            abort(403, 'Unauthorized access.');
        }

        if (!$submission->file_path) {
            abort(404, 'Submission file not found');
        }

        return Storage::disk('public')->download(
            $submission->file_path,
            $submission->file_name
        );
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        // Verify the assessment belongs to this teacher's class
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();
        $assessment = Assessment::whereIn('class_id', $classIds)->findOrFail($id);

        $assessment->delete();

        FlashMessage::success('Assessment deleted successfully.');

        return redirect()->route('teacher.assessments.index');
    }
}
