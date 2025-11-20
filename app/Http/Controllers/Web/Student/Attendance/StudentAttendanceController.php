<?php

namespace App\Http\Controllers\Web\Student\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Attendance;
use Inertia\Inertia;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.login')
                ->withErrors(['email' => 'Student record not found.']);
        }

        $query = Attendance::where('student_id', $student->id)
            ->with('classModel');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('classModel', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $attendances = $query->latest('date')->paginate(15);

        // Get statistics
        $totalAttendance = Attendance::where('student_id', $student->id)->count();
        $presentCount = Attendance::where('student_id', $student->id)->where('status', 'present')->count();
        $absentCount = Attendance::where('student_id', $student->id)->where('status', 'absent')->count();
        $lateCount = Attendance::where('student_id', $student->id)->where('status', 'late')->count();

        $stats = [
            'total_attendance' => $totalAttendance,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $lateCount,
            'attendance_percentage' => $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : 0,
        ];

        // Get classes for filter
        $classes = \App\Models\ClassModel::whereHas('enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id)->where('status', 'active');
        })->get(['id', 'name', 'code']);

        return Inertia::render('Student/Attendance/Index', [
            'attendances' => $attendances,
            'classes' => $classes,
            'stats' => $stats,
            'filters' => $request->only(['search', 'class_id', 'status', 'date_from', 'date_to']),
        ]);
    }
}
