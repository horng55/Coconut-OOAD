<?php

namespace App\Http\Controllers\Web\Teacher\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\ClassModel;
use App\Support\Service\FlashMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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
        if ($classId) {
            $class = $teacher->classes()
                ->select('classes.*')
                ->where('classes.id', $classId)
                ->where('classes.status', 'active')
                ->first();
        }

        $classes = $teacher->classes()
            ->select('classes.id', 'classes.name', 'classes.code')
            ->where('classes.status', 'active')
            ->get();

        return Inertia::render('Teacher/Assessment/Create', [
            'classes' => $classes,
            'selectedClass' => $class,
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
            'class_id' => 'required|exists:classes,id|in:' . implode(',', $classIds),
            'assessment_type' => 'required|in:quiz,assignment,exam,project,participation,mid_term',
            'assessment_name' => 'required|string|max:255',
            'score' => 'nullable|numeric|min:0',
            'max_score' => 'required|numeric|min:1',
            'assessment_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Assessment::create([
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

        FlashMessage::success('Assessment created successfully.');
        
        return redirect()->route('teacher.assessments.index');
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
