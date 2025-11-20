<?php

namespace App\Http\Controllers\Web\Admin\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Support\Service\FlashMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $query = Promotion::with([
            'student.user',
            'fromClass',
            'toClass',
            'promotedBy'
        ]);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhereHas('fromClass', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->orWhereHas('toClass', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('promotion_type')) {
            $query->where('promotion_type', $request->promotion_type);
        }

        if ($request->has('from_academic_year')) {
            $query->where('from_academic_year', $request->from_academic_year);
        }

        if ($request->has('to_academic_year')) {
            $query->where('to_academic_year', $request->to_academic_year);
        }

        $promotions = $query->latest('promotion_date')->paginate(15);

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code', 'academic_year']);
        $currentYear = date('Y');
        $defaultAcademicYear = ($currentYear - 1) . '-' . $currentYear;

        return Inertia::render('Admin/Promotion/Index', [
            'promotions' => $promotions,
            'classes' => $classes,
            'filters' => $request->only(['search', 'status', 'promotion_type', 'from_academic_year', 'to_academic_year']),
            'defaultAcademicYear' => $defaultAcademicYear,
        ]);
    }

    public function create(Request $request)
    {
        $classes = ClassModel::where('status', 'active')
            ->orderBy('academic_year')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'academic_year']);

        $students = Student::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                    'student_id' => $student->student_id,
                    'email' => $student->user->email,
                ];
            });

        $currentYear = date('Y');
        $defaultAcademicYear = ($currentYear - 1) . '-' . $currentYear;

        return Inertia::render('Admin/Promotion/Create', [
            'classes' => $classes,
            'students' => $students,
            'defaultAcademicYear' => $defaultAcademicYear,
            'selectedStudentId' => $request->get('student_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'from_class_id' => 'required|exists:classes,id',
            'to_class_id' => 'required|exists:classes,id|different:from_class_id',
            'from_academic_year' => 'required|string|max:255',
            'to_academic_year' => 'required|string|max:255',
            'promotion_date' => 'required|date',
            'promotion_type' => 'required|in:automatic,manual,conditional,repeated',
            'status' => 'required|in:pending,approved,rejected,completed',
            'notes' => 'nullable|string',
        ]);

        // Check if student is enrolled in from_class
        $enrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_id', $validated['from_class_id'])
            ->where('status', 'active')
            ->first();

        if (!$enrollment) {
            return back()->withErrors([
                'from_class_id' => 'Student is not enrolled in the selected source class.',
            ])->withInput();
        }

        // Check if student is already enrolled in to_class
        $existingEnrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_id', $validated['to_class_id'])
            ->where('status', 'active')
            ->first();

        if ($existingEnrollment) {
            return back()->withErrors([
                'to_class_id' => 'Student is already enrolled in the target class.',
            ])->withInput();
        }

        // Check class capacity
        $toClass = ClassModel::findOrFail($validated['to_class_id']);
        $currentEnrollments = $toClass->enrollments()->where('status', 'active')->count();
        if ($currentEnrollments >= $toClass->max_students) {
            return back()->withErrors([
                'to_class_id' => 'Target class has reached maximum capacity.',
            ])->withInput();
        }

        $validated['promoted_by'] = Auth::id();

        Promotion::create($validated);

        // If status is approved or completed, automatically transfer enrollment
        if (in_array($validated['status'], ['approved', 'completed'])) {
            $this->executePromotion($validated['student_id'], $validated['from_class_id'], $validated['to_class_id']);
        }

        FlashMessage::success('Promotion created successfully.');
        return redirect()->route('admin.promotions.index');
    }

    public function show($id)
    {
        $promotion = Promotion::with([
            'student.user',
            'fromClass',
            'toClass',
            'promotedBy'
        ])->findOrFail($id);

        return Inertia::render('Admin/Promotion/Show', [
            'promotion' => $promotion,
        ]);
    }

    public function approve($id)
    {
        $promotion = Promotion::findOrFail($id);

        if ($promotion->status === 'completed') {
            FlashMessage::error('Promotion is already completed.');
            return back();
        }

        DB::transaction(function () use ($promotion) {
            $promotion->update([
                'status' => 'approved',
                'promoted_by' => Auth::id(),
            ]);

            $this->executePromotion(
                $promotion->student_id,
                $promotion->from_class_id,
                $promotion->to_class_id
            );

            $promotion->update(['status' => 'completed']);
        });

        FlashMessage::success('Promotion approved and executed successfully.');
        return redirect()->route('admin.promotions.index');
    }

    public function reject($id, Request $request)
    {
        $promotion = Promotion::findOrFail($id);

        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $promotion->update([
            'status' => 'rejected',
            'notes' => $validated['notes'] ?? $promotion->notes,
        ]);

        FlashMessage::success('Promotion rejected.');
        return redirect()->route('admin.promotions.index');
    }

    public function bulkPromote(Request $request)
    {
        $validated = $request->validate([
            'from_class_id' => 'required|exists:classes,id',
            'to_class_id' => 'required|exists:classes,id|different:from_class_id',
            'from_academic_year' => 'required|string|max:255',
            'to_academic_year' => 'required|string|max:255',
            'promotion_date' => 'required|date',
            'promotion_type' => 'required|in:automatic,manual,conditional,repeated',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'notes' => 'nullable|string',
        ]);

        $fromClass = ClassModel::findOrFail($validated['from_class_id']);
        $toClass = ClassModel::findOrFail($validated['to_class_id']);

        // Check class capacity
        $currentEnrollments = $toClass->enrollments()->where('status', 'active')->count();
        $availableSlots = $toClass->max_students - $currentEnrollments;
        
        if (count($validated['student_ids']) > $availableSlots) {
            return back()->withErrors([
                'student_ids' => "Only {$availableSlots} slots available in target class. Please select fewer students.",
            ])->withInput();
        }

        $successCount = 0;
        $errors = [];

        DB::transaction(function () use ($validated, &$successCount, &$errors) {
            foreach ($validated['student_ids'] as $studentId) {
                try {
                    // Check if student is enrolled in from_class
                    $enrollment = Enrollment::where('student_id', $studentId)
                        ->where('class_id', $validated['from_class_id'])
                        ->where('status', 'active')
                        ->first();

                    if (!$enrollment) {
                        $errors[] = "Student ID {$studentId} is not enrolled in source class.";
                        continue;
                    }

                    // Check if already enrolled in to_class
                    $existingEnrollment = Enrollment::where('student_id', $studentId)
                        ->where('class_id', $validated['to_class_id'])
                        ->where('status', 'active')
                        ->first();

                    if ($existingEnrollment) {
                        $errors[] = "Student ID {$studentId} is already enrolled in target class.";
                        continue;
                    }

                    // Create promotion record
                    Promotion::create([
                        'student_id' => $studentId,
                        'from_class_id' => $validated['from_class_id'],
                        'to_class_id' => $validated['to_class_id'],
                        'from_academic_year' => $validated['from_academic_year'],
                        'to_academic_year' => $validated['to_academic_year'],
                        'promotion_date' => $validated['promotion_date'],
                        'promotion_type' => $validated['promotion_type'],
                        'status' => 'approved',
                        'promoted_by' => Auth::id(),
                        'notes' => $validated['notes'] ?? null,
                    ]);

                    // Execute promotion
                    $this->executePromotion($studentId, $validated['from_class_id'], $validated['to_class_id']);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Error promoting student ID {$studentId}: " . $e->getMessage();
                }
            }
        });

        if ($successCount > 0) {
            FlashMessage::success("Successfully promoted {$successCount} student(s).");
        }

        if (!empty($errors)) {
            return back()->withErrors(['bulk' => $errors])->withInput();
        }

        return redirect()->route('admin.promotions.index');
    }

    public function bulkPromoteView(Request $request)
    {
        $fromClassId = $request->get('from_class_id');
        
        $classes = ClassModel::where('status', 'active')
            ->orderBy('academic_year')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'academic_year']);

        $students = [];
        if ($fromClassId) {
            $students = Student::whereHas('enrollments', function ($query) use ($fromClassId) {
                $query->where('class_id', $fromClassId)
                      ->where('status', 'active');
            })
            ->with('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                    'student_id' => $student->student_id,
                    'email' => $student->user->email,
                ];
            });
        }

        $currentYear = date('Y');
        $defaultAcademicYear = ($currentYear - 1) . '-' . $currentYear;

        return Inertia::render('Admin/Promotion/BulkPromote', [
            'classes' => $classes,
            'students' => $students,
            'defaultAcademicYear' => $defaultAcademicYear,
            'selectedFromClassId' => $fromClassId,
        ]);
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);

        if ($promotion->status === 'completed') {
            FlashMessage::error('Cannot delete a completed promotion.');
            return back();
        }

        $promotion->delete();

        FlashMessage::success('Promotion deleted successfully.');
        return redirect()->route('admin.promotions.index');
    }

    /**
     * Execute the promotion by transferring enrollment
     */
    private function executePromotion($studentId, $fromClassId, $toClassId)
    {
        // Mark old enrollment as completed
        Enrollment::where('student_id', $studentId)
            ->where('class_id', $fromClassId)
            ->where('status', 'active')
            ->update(['status' => 'completed']);

        // Create new enrollment
        Enrollment::create([
            'student_id' => $studentId,
            'class_id' => $toClassId,
            'enrollment_date' => now(),
            'status' => 'active',
            'notes' => 'Promoted from previous class',
        ]);
    }
}

