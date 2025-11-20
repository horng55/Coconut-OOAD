<?php

namespace App\Http\Controllers\Web\Teacher\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
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

        $query = Attendance::whereIn('class_id', $classIds)
            ->with('student.user', 'classModel', 'markedBy');

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

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('date')->paginate(15);

        $classes = $teacher->classes()
            ->select('classes.id', 'classes.name', 'classes.code')
            ->where('classes.status', 'active')
            ->get();

        return Inertia::render('Teacher/Attendance/Index', [
            'attendances' => $attendances,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'date', 'status']),
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
        $date = $request->date ?? now()->format('Y-m-d');

        $class = null;
        $students = collect();

        if ($classId) {
            // Only allow access to teacher's classes
            $class = $teacher->classes()
                ->select('classes.*')
                ->where('classes.id', $classId)
                ->where('classes.status', 'active')
                ->with('enrollments.student.user')
                ->first();

            if ($class) {
                $students = $class->enrollments()
                    ->where('status', 'active')
                    ->with('student.user')
                    ->get()
                    ->map(function ($enrollment) use ($date) {
                        $attendance = Attendance::where('student_id', $enrollment->student_id)
                            ->where('class_id', $enrollment->class_id)
                            ->whereDate('date', $date)
                            ->first();

                        return [
                            'id' => $enrollment->student->id,
                            'name' => $enrollment->student->user->full_name,
                            'student_id' => $enrollment->student->student_id,
                            'attendance' => $attendance ? [
                                'id' => $attendance->id,
                                'status' => $attendance->status,
                                'notes' => $attendance->notes,
                            ] : null,
                        ];
                    });
            }
        }

        $classes = $teacher->classes()
            ->select('classes.id', 'classes.name', 'classes.code')
            ->where('classes.status', 'active')
            ->get();

        return Inertia::render('Teacher/Attendance/Create', [
            'classes' => $classes,
            'selectedClass' => $class,
            'students' => $students,
            'date' => $date,
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
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.notes' => 'nullable|string',
        ]);

        foreach ($validated['attendances'] as $attendanceData) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $attendanceData['student_id'],
                    'class_id' => $validated['class_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $attendanceData['status'],
                    'notes' => $attendanceData['notes'] ?? null,
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return redirect()->route('teacher.attendances.index')
            ->with('success', 'Attendance marked successfully.');
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

        // Get the attendance and verify it belongs to teacher's class
        $attendance = Attendance::whereIn('class_id', $classIds)
            ->with('student.user', 'classModel')
            ->findOrFail($id);

        return Inertia::render('Teacher/Attendance/Edit', [
            'attendance' => $attendance,
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

        // Verify the attendance belongs to this teacher's class
        $classIds = $teacher->classes()->select('classes.id')->pluck('id')->toArray();
        $attendance = Attendance::whereIn('class_id', $classIds)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'marked_by' => Auth::id(),
        ]);

        return redirect()->route('teacher.attendances.index')
            ->with('success', 'Attendance updated successfully.');
    }
}

