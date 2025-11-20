<?php

namespace App\Http\Controllers\Web\Admin\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('student.user', 'classModel.teachers.user', 'markedBy');

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

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('date')->paginate(15);

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Attendance/Index', [
            'attendances' => $attendances,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'date', 'status']),
        ]);
    }

    public function create(Request $request)
    {
        $classId = $request->class_id;
        $date = $request->date ?? now()->format('Y-m-d');

        $class = null;
        $students = collect();

        if ($classId) {
            $class = ClassModel::with('enrollments.student.user')
                ->where('status', 'active')
                ->findOrFail($classId);

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
                        'name' => $enrollment->student->user->name,
                        'student_id' => $enrollment->student->student_id,
                        'attendance' => $attendance ? [
                            'id' => $attendance->id,
                            'status' => $attendance->status,
                            'notes' => $attendance->notes,
                        ] : null,
                    ];
                });
        }

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Attendance/Create', [
            'classes' => $classes,
            'selectedClass' => $class,
            'students' => $students,
            'date' => $date,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
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

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance marked successfully.');
    }

    public function show($id)
    {
        $attendance = Attendance::with('student.user', 'classModel.teachers.user', 'markedBy')
            ->findOrFail($id);
        return Inertia::render('Admin/Attendance/Show', [
            'attendance' => $attendance,
        ]);
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'marked_by' => Auth::id(),
        ]);

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance updated successfully.');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('admin.attendances.index')
            ->with('success', 'Attendance deleted successfully.');
    }
}
