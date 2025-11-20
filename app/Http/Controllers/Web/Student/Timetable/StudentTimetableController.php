<?php

namespace App\Http\Controllers\Web\Student\Timetable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Timetable;
use Inertia\Inertia;

class StudentTimetableController extends Controller
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
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id')
            ->toArray();

        $query = Timetable::whereIn('class_id', $classIds)
            ->where('status', 'active')
            ->with(['classModel', 'subject', 'teacher.user']);

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $timetables = $query->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        // Group by day
        $groupedTimetables = $timetables->groupBy('day_of_week');

        // Get classes for filter
        $classes = \App\Models\ClassModel::whereIn('id', $classIds)
            ->where('status', 'active')
            ->get(['id', 'name', 'code']);

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return Inertia::render('Student/Timetable/Index', [
            'timetables' => $timetables,
            'groupedTimetables' => $groupedTimetables,
            'classes' => $classes,
            'daysOfWeek' => $daysOfWeek,
            'filters' => $request->only(['class_id', 'day_of_week', 'academic_year']),
        ]);
    }
}
