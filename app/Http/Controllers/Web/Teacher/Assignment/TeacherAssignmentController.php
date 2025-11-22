<?php

namespace App\Http\Controllers\Web\Teacher\Assignment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Teacher;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassModel;
use App\Models\Enrollment;
use Inertia\Inertia;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $query = Assignment::where('teacher_id', $teacher->id)
            ->with(['classModel.subjects', 'submissions']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $assignments = $query->latest('created_at')->paginate(15);

        $classes = $teacher->classes()
            ->where('status', 'active')
            ->select('id', 'name', 'code', 'academic_year')
            ->get();

        return Inertia::render('Teacher/Assignment/Index', [
            'assignments' => $assignments,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'status']),
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $classes = $teacher->classes()
            ->where('status', 'active')
            ->with('subjects')
            ->get();

        $preselectedClassId = $request->query('class_id');

        return Inertia::render('Teacher/Assignment/Create', [
            'classes' => $classes,
            'preselectedClassId' => $preselectedClassId,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'required|date',
            'due_time' => 'nullable|date_format:H:i',
            'max_score' => 'required|numeric|min:0',
            'attachment' => 'nullable|file|max:10240', // 10MB max
            'status' => 'required|in:draft,published,closed',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only([
                'class_id',
                'title',
                'description',
                'instructions',
                'due_date',
                'due_time',
                'max_score',
                'status',
            ]);

            $data['teacher_id'] = $teacher->id;

            // Handle file upload
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $path = $file->store('assignments', 'public');
                $data['attachment_path'] = $path;
            }

            $assignment = Assignment::create($data);

            DB::commit();

            return redirect()->route('teacher.assignments.show', $assignment->id)
                ->with('success', 'Assignment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create assignment: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $assignment = Assignment::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with([
                'classModel.subjects',
                'submissions' => function($query) {
                    $query->with('student.user')->latest('submitted_at');
                }
            ])
            ->firstOrFail();

        // Get enrolled students count
        $totalStudents = Enrollment::where('class_id', $assignment->class_id)
            ->where('status', 'active')
            ->count();

        $submittedCount = $assignment->submissions->count();
        $gradedCount = $assignment->submissions->where('status', 'graded')->count();

        return Inertia::render('Teacher/Assignment/Show', [
            'assignment' => $assignment,
            'totalStudents' => $totalStudents,
            'submittedCount' => $submittedCount,
            'gradedCount' => $gradedCount,
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $assignment = Assignment::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('classModel')
            ->firstOrFail();

        $classes = $teacher->classes()
            ->where('status', 'active')
            ->with('subjects')
            ->get();

        return Inertia::render('Teacher/Assignment/Edit', [
            'assignment' => $assignment,
            'classes' => $classes,
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $assignment = Assignment::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'required|date',
            'due_time' => 'nullable|date_format:H:i',
            'max_score' => 'required|numeric|min:0',
            'attachment' => 'nullable|file|max:10240',
            'status' => 'required|in:draft,published,closed',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->only([
                'class_id',
                'title',
                'description',
                'instructions',
                'due_date',
                'due_time',
                'max_score',
                'status',
            ]);

            // Handle file upload
            if ($request->hasFile('attachment')) {
                // Delete old file if exists
                if ($assignment->attachment_path) {
                    Storage::disk('public')->delete($assignment->attachment_path);
                }
                
                $file = $request->file('attachment');
                $path = $file->store('assignments', 'public');
                $data['attachment_path'] = $path;
            }

            $assignment->update($data);

            DB::commit();

            return redirect()->route('teacher.assignments.show', $assignment->id)
                ->with('success', 'Assignment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update assignment: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $assignment = Assignment::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            // Delete attachment file if exists
            if ($assignment->attachment_path) {
                Storage::disk('public')->delete($assignment->attachment_path);
            }

            $assignment->delete();

            DB::commit();

            return redirect()->route('teacher.assignments.index')
                ->with('success', 'Assignment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete assignment: ' . $e->getMessage()]);
        }
    }

    public function gradeSubmission(Request $request, $assignmentId, $submissionId)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $assignment = Assignment::where('id', $assignmentId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $submission = AssignmentSubmission::where('id', $submissionId)
            ->where('assignment_id', $assignmentId)
            ->with('student.user')
            ->firstOrFail();

        $request->validate([
            'score' => 'required|numeric|min:0|max:' . $assignment->max_score,
            'teacher_feedback' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $submission->update([
                'score' => $request->score,
                'teacher_feedback' => $request->teacher_feedback,
                'status' => 'graded',
                'graded_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Submission graded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to grade submission: ' . $e->getMessage()]);
        }
    }

    public function downloadAttachment($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            abort(403, 'Unauthorized');
        }

        $assignment = Assignment::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        if (!$assignment->attachment_path) {
            abort(404, 'Attachment not found');
        }

        return Storage::disk('public')->download($assignment->attachment_path);
    }

    public function downloadSubmission($assignmentId, $submissionId)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            abort(403, 'Unauthorized');
        }

        $assignment = Assignment::where('id', $assignmentId)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $submission = AssignmentSubmission::where('id', $submissionId)
            ->where('assignment_id', $assignmentId)
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
