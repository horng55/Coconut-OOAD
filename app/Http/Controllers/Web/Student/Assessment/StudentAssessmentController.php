<?php

namespace App\Http\Controllers\Web\Student\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StudentAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $student = Student::with('user')
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Get all classes the student is enrolled in
        $enrolledClassIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id');

        // Get assessments for enrolled classes
        $query = Assessment::with(['classModel', 'createdBy', 'grades' => function ($query) use ($student) {
            $query->where('student_id', $student->id);
        }])
            ->whereIn('class_id', $enrolledClassIds)
            ->where('status', 'active')
            ->orderBy('assessment_date', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('assessment_name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('classModel', function ($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('assessment_type')) {
            $query->where('assessment_type', $request->assessment_type);
        }

        $assessments = $query->paginate(10)->withQueryString();

        // Get student's enrolled classes for filter
        $classes = $student->enrollments()
            ->with('classModel')
            ->where('status', 'active')
            ->get()
            ->pluck('classModel')
            ->unique('id');

        return Inertia::render('Student/Assessment/Index', [
            'assessments' => $assessments,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'assessment_type']),
        ]);
    }

    public function show($id)
    {
        $student = Student::with('user')
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $assessment = Assessment::with([
            'classModel',
            'createdBy',
            'grades' => function ($query) use ($student) {
                $query->where('student_id', $student->id)->with('gradedBy');
            },
            'submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }
        ])->findOrFail($id);

        // Check if student is enrolled in the assessment's class
        $isEnrolled = $student->enrollments()
            ->where('class_id', $assessment->class_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in this class.');
        }

        // Get student's grade for this assessment
        $myGrade = $assessment->grades->first();
        
        // Get student's submission for this assessment
        $mySubmission = $assessment->submissions->first();

        return Inertia::render('Student/Assessment/Show', [
            'assessment' => $assessment,
            'myGrade' => $myGrade,
            'mySubmission' => $mySubmission,
        ]);
    }

    public function submit(Request $request, $id)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        $assessment = Assessment::findOrFail($id);

        // Check if student is enrolled in the assessment's class
        $isEnrolled = $student->enrollments()
            ->where('class_id', $assessment->class_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'You are not enrolled in this class.');
        }

        // Check if already submitted
        $existingSubmission = AssessmentSubmission::where('assessment_id', $id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingSubmission) {
            return redirect()->back()
                ->withErrors(['error' => 'You have already submitted this assessment.']);
        }

        $request->validate([
            'comments' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:10240', // PDF only, 10MB max
        ]);

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $path = $file->store('assessment_submissions', 'public');

            AssessmentSubmission::create([
                'assessment_id' => $id,
                'student_id' => $student->id,
                'comments' => $request->comments,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'submitted_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('student.assessments.show', $id)
                ->with('success', 'Assessment submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to submit assessment: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function downloadSubmission($id)
    {
        $student = Student::where('user_id', auth()->id())->firstOrFail();

        $submission = AssessmentSubmission::where('assessment_id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if (!$submission->file_path) {
            abort(404, 'Submission file not found');
        }

        return Storage::disk('public')->download(
            $submission->file_path,
            $submission->file_name
        );
    }
}
