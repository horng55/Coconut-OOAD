<?php

namespace App\Http\Controllers\Web\Admin\Timetable;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Teacher;
use App\Support\Service\FlashMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $query = Timetable::with(['classModel', 'subject', 'teacher.user']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('classModel', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->orWhereHas('subject', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->orWhereHas('teacher.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->orWhere('room', 'like', "%{$search}%");
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $timetables = $query->orderBy('day_of_week')
            ->orderBy('start_time')
            ->paginate(15);

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);
        $currentYear = date('Y');
        $defaultAcademicYear = ($currentYear - 1) . '-' . $currentYear;

        return Inertia::render('Admin/Timetable/Index', [
            'timetables' => $timetables,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'day_of_week', 'academic_year', 'status']),
            'defaultAcademicYear' => $defaultAcademicYear,
        ]);
    }

    public function create(Request $request)
    {
        $classes = ClassModel::where('status', 'active')
            ->with('subjects', 'teachers.user')
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'code' => $class->code,
                    'subjects' => $class->subjects->map(function ($subject) {
                        return [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'code' => $subject->code,
                        ];
                    }),
                    'teachers' => $class->teachers->map(function ($teacher) {
                        return [
                            'id' => $teacher->id,
                            'name' => $teacher->user->full_name,
                            'employee_id' => $teacher->employee_id,
                        ];
                    }),
                ];
            });

        $allSubjects = Subject::where('status', 'active')->get(['id', 'name', 'code']);
        $allTeachers = Teacher::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->user->full_name,
                    'employee_id' => $teacher->employee_id,
                ];
            });

        $currentYear = date('Y');
        $defaultAcademicYear = ($currentYear - 1) . '-' . $currentYear;

        return Inertia::render('Admin/Timetable/Create', [
            'classes' => $classes,
            'allSubjects' => $allSubjects,
            'allTeachers' => $allTeachers,
            'defaultAcademicYear' => $defaultAcademicYear,
            'selectedClassId' => $request->get('class_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:255',
            'academic_year' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        // Check for time conflicts
        $conflict = Timetable::where('class_id', $validated['class_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('academic_year', $validated['academic_year'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->where('status', 'active')
            ->first();

        if ($conflict) {
            return back()->withErrors([
                'time' => 'There is a time conflict with an existing timetable entry.',
            ])->withInput();
        }

        Timetable::create($validated);

        FlashMessage::success('Timetable entry created successfully.');
        return redirect()->route('admin.timetables.index');
    }

    public function show($id)
    {
        $timetable = Timetable::with(['classModel', 'subject', 'teacher.user'])->findOrFail($id);

        return Inertia::render('Admin/Timetable/Show', [
            'timetable' => $timetable,
        ]);
    }

    public function edit($id)
    {
        $timetable = Timetable::with(['classModel', 'subject', 'teacher'])->findOrFail($id);

        $classes = ClassModel::where('status', 'active')
            ->with('subjects', 'teachers.user')
            ->get()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'code' => $class->code,
                    'subjects' => $class->subjects->map(function ($subject) {
                        return [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'code' => $subject->code,
                        ];
                    }),
                    'teachers' => $class->teachers->map(function ($teacher) {
                        return [
                            'id' => $teacher->id,
                            'name' => $teacher->user->full_name,
                            'employee_id' => $teacher->employee_id,
                        ];
                    }),
                ];
            });

        $allSubjects = Subject::where('status', 'active')->get(['id', 'name', 'code']);
        $allTeachers = Teacher::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->user->full_name,
                    'employee_id' => $teacher->employee_id,
                ];
            });

        return Inertia::render('Admin/Timetable/Edit', [
            'timetable' => $timetable,
            'classes' => $classes,
            'allSubjects' => $allSubjects,
            'allTeachers' => $allTeachers,
        ]);
    }

    public function update(Request $request, $id)
    {
        $timetable = Timetable::findOrFail($id);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room' => 'nullable|string|max:255',
            'academic_year' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        // Check for time conflicts (excluding current timetable)
        $conflict = Timetable::where('class_id', $validated['class_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('academic_year', $validated['academic_year'])
            ->where('id', '!=', $id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->where('status', 'active')
            ->first();

        if ($conflict) {
            return back()->withErrors([
                'time' => 'There is a time conflict with an existing timetable entry.',
            ])->withInput();
        }

        $timetable->update($validated);

        FlashMessage::success('Timetable entry updated successfully.');
        return redirect()->route('admin.timetables.index');
    }

    public function destroy($id)
    {
        $timetable = Timetable::findOrFail($id);
        $timetable->delete();

        FlashMessage::success('Timetable entry deleted successfully.');
        return redirect()->route('admin.timetables.index');
    }
}

