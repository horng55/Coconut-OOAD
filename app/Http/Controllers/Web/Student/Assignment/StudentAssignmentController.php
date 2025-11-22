<?php

namespace App\Http\Controllers\Web\Student\Assignment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use Inertia\Inertia;

class StudentAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.login')
                ->withErrors(['email' => 'Student record not found.']);
        }

        // Get student's enrolled class IDs
        $classIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->pluck('class_id');

        $query = Assignment::whereIn('class_id', $classIds)
            ->where('status', 'published')
            ->with([
                'classModel',
                'teacher.user',
                'submissions' => function($query) use ($student) {
                    $query->where('student_id', $student->id);
                }
            ]);

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

        if ($request->has('status_filter')) {
            $statusFilter = $request->status_filter;
            if ($statusFilter === 'pending') {
                $query->whereDoesntHave('submissions', function($q) use ($student) {
                    $q->where('student_id', $student->id);
                });
            } elseif ($statusFilter === 'submitted') {
                $query->whereHas('submissions', function($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->where('status', 'submitted');
                });
            } elseif ($statusFilter === 'graded') {
                $query->whereHas('submissions', function($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->where('status', 'graded');
                });
            }
        }

        $assignments = $query->latest('due_date')->paginate(15);

        // Get student's classes for filter
        $classes = $student->enrollments()
            ->where('enrollments.status', 'active')
            ->with('class')
            ->get()
            ->pluck('class')
            ->unique('id');

        return Inertia::render('Student/Assignment/Index', [
            'assignments' => $assignments,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'status_filter']),
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

        // Verify student is enrolled in the class
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->whereHas('class.assignments', function($query) use ($id) {
                $query->where('id', $id);
            })
            ->exists();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this class.');
        }

        $assignment = Assignment::where('id', $id)
            ->where('status', 'published')
            ->with([
                'classModel',
                'teacher.user',
                'submissions' => function($query) use ($student) {
                    $query->where('student_id', $student->id);
                }
            ])
            ->firstOrFail();

        $submission = $assignment->submissions->first();

        return Inertia::render('Student/Assignment/Show', [
            'assignment' => $assignment,
            'submission' => $submission,
        ]);
    }

    public function submit(Request $request, $id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.login')
                ->withErrors(['email' => 'Student record not found.']);
        }

        // Verify student is enrolled in the class
        $assignment = Assignment::where('id', $id)
            ->where('status', 'published')
            ->firstOrFail();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('class_id', $assignment->class_id)
            ->where('status', 'active')
            ->exists();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this class.');
        }

        // Check if already submitted
        $existingSubmission = AssignmentSubmission::where('assignment_id', $id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingSubmission) {
            return redirect()->back()
                ->withErrors(['error' => 'You have already submitted this assignment.']);
        }

        $request->validate([
            'comments' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        DB::beginTransaction();

        try {
            $file = $request->file('file');
            $path = $file->store('submissions', 'public');

            AssignmentSubmission::create([
                'assignment_id' => $id,
                'student_id' => $student->id,
                'comments' => $request->comments,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('student.assignments.show', $id)
                ->with('success', 'Assignment submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to submit assignment: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function downloadAttachment($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            abort(403, 'Unauthorized');
        }

        $assignment = Assignment::where('id', $id)
            ->where('status', 'published')
            ->firstOrFail();

        // Verify student is enrolled in the class
        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('class_id', $assignment->class_id)
            ->where('status', 'active')
            ->exists();

        if (!$enrollment) {
            abort(403, 'You are not enrolled in this class.');
        }

        if (!$assignment->attachment_path) {
            abort(404, 'Attachment not found');
        }

        return Storage::disk('public')->download($assignment->attachment_path);
    }
}
