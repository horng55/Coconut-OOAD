<?php

namespace App\Http\Controllers\Web\Teacher\Timetable;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Timetable;
use Inertia\Inertia;

class TeacherTimetableController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $query = Timetable::where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->with(['classModel', 'subject']);

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
        $classes = \App\Models\ClassModel::whereHas('teachers', function ($q) use ($teacher) {
            $q->where('teachers.id', $teacher->id);
        })
        ->where('status', 'active')
        ->get(['id', 'name', 'code']);

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return Inertia::render('Teacher/Timetable/Index', [
            'timetables' => $timetables,
            'groupedTimetables' => $groupedTimetables,
            'classes' => $classes,
            'daysOfWeek' => $daysOfWeek,
            'filters' => $request->only(['class_id', 'day_of_week', 'academic_year']),
        ]);
    }
}
