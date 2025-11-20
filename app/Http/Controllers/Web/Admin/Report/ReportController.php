<?php

namespace App\Http\Controllers\Web\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Teacher;
use App\Models\Enrollment;
use App\Support\Service\FlashMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with('generatedBy');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('report_type')) {
            $query->where('report_type', $request->report_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest('generated_at')->paginate(15);

        return Inertia::render('Admin/Report/Index', [
            'reports' => $reports,
            'filters' => $request->only(['search', 'report_type', 'status']),
        ]);
    }

    public function create()
    {
        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code', 'academic_year']);
        
        $students = Student::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'user_id' => $student->user_id,
                    'student_id' => $student->student_id,
                    'user' => $student->user ? [
                        'id' => $student->user->id,
                        'full_name' => $student->user->full_name,
                        'email' => $student->user->email,
                    ] : null,
                ];
            });
        
        $teachers = Teacher::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'user_id' => $teacher->user_id,
                    'employee_id' => $teacher->employee_id,
                    'user' => $teacher->user ? [
                        'id' => $teacher->user->id,
                        'full_name' => $teacher->user->full_name,
                        'email' => $teacher->user->email,
                    ] : null,
                ];
            });

        $currentYear = date('Y');
        $defaultAcademicYear = ($currentYear - 1) . '-' . $currentYear;

        return Inertia::render('Admin/Report/Create', [
            'classes' => $classes,
            'students' => $students,
            'teachers' => $teachers,
            'defaultAcademicYear' => $defaultAcademicYear,
        ]);
    }

    public function generateStudentPerformance(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'nullable|exists:classes,id',
            'academic_year' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $student = Student::with('user')->findOrFail($validated['student_id']);
        
        $query = Grade::with(['classModel', 'assessment'])
            ->where('student_id', $validated['student_id']);

        if (!empty($validated['class_id'])) {
            $query->where('class_id', $validated['class_id']);
        }

        if (!empty($validated['start_date'])) {
            $query->where('assessment_date', '>=', $validated['start_date']);
        }

        if (!empty($validated['end_date'])) {
            $query->where('assessment_date', '<=', $validated['end_date']);
        }

        $grades = $query->orderBy('assessment_date')->get();
        
        // Calculate statistics
        $totalAssessments = $grades->count();
        $averageScore = $grades->avg('percentage');
        $totalScore = $grades->sum('score');
        $totalMaxScore = $grades->sum('max_score');

        // Attendance statistics
        $attendanceQuery = Attendance::where('student_id', $validated['student_id']);
        if (!empty($validated['class_id'])) {
            $attendanceQuery->where('class_id', $validated['class_id']);
        }
        if (!empty($validated['start_date'])) {
            $attendanceQuery->where('date', '>=', $validated['start_date']);
        }
        if (!empty($validated['end_date'])) {
            $attendanceQuery->where('date', '<=', $validated['end_date']);
        }
        $attendances = $attendanceQuery->get();
        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

        $report = Report::create([
            'report_type' => 'student_performance',
            'title' => "Student Performance Report - {$student->user->full_name}",
            'description' => "Academic performance and attendance report for {$student->user->full_name}",
            'parameters' => $validated,
            'generated_by' => Auth::id(),
            'status' => 'completed',
            'generated_at' => now(),
        ]);

        return Inertia::render('Admin/Report/ViewStudentPerformance', [
            'report' => $report,
            'student' => $student,
            'grades' => $grades,
            'attendances' => $attendances,
            'statistics' => [
                'total_assessments' => $totalAssessments,
                'average_score' => round($averageScore, 2),
                'total_score' => round($totalScore, 2),
                'total_max_score' => round($totalMaxScore, 2),
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'attendance_rate' => round($attendanceRate, 2),
            ],
        ]);
    }

    public function generateClassPerformance(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'academic_year' => 'nullable|string',
        ]);

        $class = ClassModel::with('students.user', 'teachers.user', 'subjects')->findOrFail($validated['class_id']);

        $students = $class->students()->with('user')->get();
        
        $classStatistics = [];
        foreach ($students as $student) {
            $grades = Grade::where('student_id', $student->id)
                ->where('class_id', $validated['class_id'])
                ->get();

            $averageScore = $grades->avg('percentage');
            $totalAssessments = $grades->count();

            $attendances = Attendance::where('student_id', $student->id)
                ->where('class_id', $validated['class_id'])
                ->get();

            $totalDays = $attendances->count();
            $presentDays = $attendances->where('status', 'present')->count();
            $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

            $classStatistics[] = [
                'student' => $student,
                'average_score' => round($averageScore ?? 0, 2),
                'total_assessments' => $totalAssessments,
                'attendance_rate' => round($attendanceRate, 2),
                'total_days' => $totalDays,
                'present_days' => $presentDays,
            ];
        }

        // Sort by average score descending
        usort($classStatistics, function($a, $b) {
            return $b['average_score'] <=> $a['average_score'];
        });

        $report = Report::create([
            'report_type' => 'class_performance',
            'title' => "Class Performance Report - {$class->name}",
            'description' => "Overall class performance report for {$class->name}",
            'parameters' => $validated,
            'generated_by' => Auth::id(),
            'status' => 'completed',
            'generated_at' => now(),
        ]);

        return Inertia::render('Admin/Report/ViewClassPerformance', [
            'report' => $report,
            'class' => $class,
            'statistics' => $classStatistics,
        ]);
    }

    public function generateAttendance(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'student_id' => 'nullable|exists:students,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'nullable|in:present,absent,late,excused',
        ]);

        $query = Attendance::with(['student.user', 'classModel']);

        if (!empty($validated['class_id'])) {
            $query->where('class_id', $validated['class_id']);
        }

        if (!empty($validated['student_id'])) {
            $query->where('student_id', $validated['student_id']);
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $attendances = $query->whereBetween('date', [$validated['start_date'], $validated['end_date']])
            ->orderBy('date')
            ->orderBy('student_id')
            ->get();

        // Calculate statistics
        $totalRecords = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $excusedCount = $attendances->where('status', 'excused')->count();

        $report = Report::create([
            'report_type' => 'attendance',
            'title' => 'Attendance Report',
            'description' => "Attendance report from {$validated['start_date']} to {$validated['end_date']}",
            'parameters' => $validated,
            'generated_by' => Auth::id(),
            'status' => 'completed',
            'generated_at' => now(),
        ]);

        return Inertia::render('Admin/Report/ViewAttendance', [
            'report' => $report,
            'attendances' => $attendances,
            'statistics' => [
                'total_records' => $totalRecords,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'late_count' => $lateCount,
                'excused_count' => $excusedCount,
                'present_rate' => $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 2) : 0,
            ],
        ]);
    }

    public function generateGradeDistribution(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'assessment_type' => 'nullable|string',
            'academic_year' => 'nullable|string',
        ]);

        $query = Grade::with(['student.user', 'classModel', 'assessment']);

        if (!empty($validated['class_id'])) {
            $query->where('class_id', $validated['class_id']);
        }

        if (!empty($validated['assessment_type'])) {
            $query->where('assessment_type', $validated['assessment_type']);
        }

        $grades = $query->get();

        // Grade distribution
        $distribution = [
            'A' => $grades->where('letter_grade', 'A')->count(),
            'B' => $grades->where('letter_grade', 'B')->count(),
            'C' => $grades->where('letter_grade', 'C')->count(),
            'D' => $grades->where('letter_grade', 'D')->count(),
            'F' => $grades->where('letter_grade', 'F')->count(),
        ];

        $averageScore = $grades->avg('percentage');
        $highestScore = $grades->max('percentage');
        $lowestScore = $grades->min('percentage');

        $report = Report::create([
            'report_type' => 'grade_distribution',
            'title' => 'Grade Distribution Report',
            'description' => 'Grade distribution and statistics report',
            'parameters' => $validated,
            'generated_by' => Auth::id(),
            'status' => 'completed',
            'generated_at' => now(),
        ]);

        return Inertia::render('Admin/Report/ViewGradeDistribution', [
            'report' => $report,
            'grades' => $grades,
            'distribution' => $distribution,
            'statistics' => [
                'total_grades' => $grades->count(),
                'average_score' => round($averageScore ?? 0, 2),
                'highest_score' => round($highestScore ?? 0, 2),
                'lowest_score' => round($lowestScore ?? 0, 2),
            ],
        ]);
    }

    public function generateTeacherWorkload(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year' => 'nullable|string',
        ]);

        $query = Teacher::with(['user', 'classes']);

        if (!empty($validated['teacher_id'])) {
            $query->where('id', $validated['teacher_id']);
        }

        $teachers = $query->where('status', 'active')->get();

        $workloadData = [];
        foreach ($teachers as $teacher) {
            $classes = $teacher->classes()->where('status', 'active')->get();
            $totalStudents = 0;
            foreach ($classes as $class) {
                $totalStudents += $class->enrollments()->where('status', 'active')->count();
            }

            $workloadData[] = [
                'teacher' => $teacher,
                'total_classes' => $classes->count(),
                'total_students' => $totalStudents,
                'classes' => $classes,
            ];
        }

        $report = Report::create([
            'report_type' => 'teacher_workload',
            'title' => 'Teacher Workload Report',
            'description' => 'Teacher workload and class assignments report',
            'parameters' => $validated,
            'generated_by' => Auth::id(),
            'status' => 'completed',
            'generated_at' => now(),
        ]);

        return Inertia::render('Admin/Report/ViewTeacherWorkload', [
            'report' => $report,
            'workloadData' => $workloadData,
        ]);
    }

    public function generateEnrollment(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $query = Enrollment::with(['student.user', 'classModel'])->where('status', 'active');

        if (!empty($validated['academic_year'])) {
            $query->whereHas('classModel', function ($q) use ($validated) {
                $q->where('academic_year', $validated['academic_year']);
            });
        }

        if (!empty($validated['class_id'])) {
            $query->where('class_id', $validated['class_id']);
        }

        $enrollments = $query->get();

        // Statistics by class
        $classStats = [];
        $enrollments->groupBy('class_id')->each(function ($classEnrollments, $classId) use (&$classStats) {
            $class = $classEnrollments->first()->classModel;
            $classStats[] = [
                'class' => $class,
                'total_students' => $classEnrollments->count(),
                'max_students' => $class->max_students ?? 0,
                'capacity_percentage' => $class->max_students > 0 
                    ? round(($classEnrollments->count() / $class->max_students) * 100, 2) 
                    : 0,
            ];
        });

        $report = Report::create([
            'report_type' => 'enrollment',
            'title' => 'Enrollment Report',
            'description' => 'Student enrollment statistics report',
            'parameters' => $validated,
            'generated_by' => Auth::id(),
            'status' => 'completed',
            'generated_at' => now(),
        ]);

        return Inertia::render('Admin/Report/ViewEnrollment', [
            'report' => $report,
            'enrollments' => $enrollments,
            'classStats' => $classStats,
            'totalEnrollments' => $enrollments->count(),
        ]);
    }

    public function show($id)
    {
        $report = Report::with('generatedBy')->findOrFail($id);
        
        // Re-generate the report data based on stored parameters
        $parameters = $report->parameters ?? [];
        
        switch ($report->report_type) {
            case 'student_performance':
                return $this->showStudentPerformance($report, $parameters);
            case 'class_performance':
                return $this->showClassPerformance($report, $parameters);
            case 'attendance':
                return $this->showAttendance($report, $parameters);
            case 'grade_distribution':
                return $this->showGradeDistribution($report, $parameters);
            case 'teacher_workload':
                return $this->showTeacherWorkload($report, $parameters);
            case 'enrollment':
                return $this->showEnrollment($report, $parameters);
            default:
                return redirect()->route('admin.reports.index')
                    ->with('error', 'Report type not supported.');
        }
    }

    private function showStudentPerformance($report, $parameters)
    {
        if (empty($parameters['student_id'])) {
            return redirect()->route('admin.reports.index')
                ->with('error', 'Invalid report parameters.');
        }

        $student = Student::with('user')->findOrFail($parameters['student_id']);
        
        $query = Grade::with(['classModel', 'assessment'])
            ->where('student_id', $parameters['student_id']);

        if (!empty($parameters['class_id'])) {
            $query->where('class_id', $parameters['class_id']);
        }

        if (!empty($parameters['start_date'])) {
            $query->where('assessment_date', '>=', $parameters['start_date']);
        }

        if (!empty($parameters['end_date'])) {
            $query->where('assessment_date', '<=', $parameters['end_date']);
        }

        $grades = $query->orderBy('assessment_date')->get();
        
        $totalAssessments = $grades->count();
        $averageScore = $grades->avg('percentage');
        $totalScore = $grades->sum('score');
        $totalMaxScore = $grades->sum('max_score');

        $attendanceQuery = Attendance::where('student_id', $parameters['student_id']);
        if (!empty($parameters['class_id'])) {
            $attendanceQuery->where('class_id', $parameters['class_id']);
        }
        if (!empty($parameters['start_date'])) {
            $attendanceQuery->where('date', '>=', $parameters['start_date']);
        }
        if (!empty($parameters['end_date'])) {
            $attendanceQuery->where('date', '<=', $parameters['end_date']);
        }
        $attendances = $attendanceQuery->get();
        $totalDays = $attendances->count();
        $presentDays = $attendances->where('status', 'present')->count();
        $absentDays = $attendances->where('status', 'absent')->count();
        $lateDays = $attendances->where('status', 'late')->count();
        $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

        return Inertia::render('Admin/Report/ViewStudentPerformance', [
            'report' => $report,
            'student' => $student,
            'grades' => $grades,
            'attendances' => $attendances,
            'statistics' => [
                'total_assessments' => $totalAssessments,
                'average_score' => round($averageScore, 2),
                'total_score' => round($totalScore, 2),
                'total_max_score' => round($totalMaxScore, 2),
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'attendance_rate' => round($attendanceRate, 2),
            ],
        ]);
    }

    private function showClassPerformance($report, $parameters)
    {
        if (empty($parameters['class_id'])) {
            return redirect()->route('admin.reports.index')
                ->with('error', 'Invalid report parameters.');
        }

        $class = ClassModel::with('students.user', 'teachers.user', 'subjects')->findOrFail($parameters['class_id']);
        $students = $class->students()->with('user')->get();
        
        $classStatistics = [];
        foreach ($students as $student) {
            $grades = Grade::where('student_id', $student->id)
                ->where('class_id', $parameters['class_id'])
                ->get();

            $averageScore = $grades->avg('percentage');
            $totalAssessments = $grades->count();

            $attendances = Attendance::where('student_id', $student->id)
                ->where('class_id', $parameters['class_id'])
                ->get();

            $totalDays = $attendances->count();
            $presentDays = $attendances->where('status', 'present')->count();
            $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

            $classStatistics[] = [
                'student' => $student,
                'average_score' => round($averageScore ?? 0, 2),
                'total_assessments' => $totalAssessments,
                'attendance_rate' => round($attendanceRate, 2),
                'total_days' => $totalDays,
                'present_days' => $presentDays,
            ];
        }

        usort($classStatistics, function($a, $b) {
            return $b['average_score'] <=> $a['average_score'];
        });

        return Inertia::render('Admin/Report/ViewClassPerformance', [
            'report' => $report,
            'class' => $class,
            'statistics' => $classStatistics,
        ]);
    }

    private function showAttendance($report, $parameters)
    {
        $query = Attendance::with(['student.user', 'classModel']);

        if (!empty($parameters['class_id'])) {
            $query->where('class_id', $parameters['class_id']);
        }

        if (!empty($parameters['student_id'])) {
            $query->where('student_id', $parameters['student_id']);
        }

        if (!empty($parameters['status'])) {
            $query->where('status', $parameters['status']);
        }

        $attendances = $query->whereBetween('date', [$parameters['start_date'], $parameters['end_date']])
            ->orderBy('date')
            ->orderBy('student_id')
            ->get();

        $totalRecords = $attendances->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $excusedCount = $attendances->where('status', 'excused')->count();

        return Inertia::render('Admin/Report/ViewAttendance', [
            'report' => $report,
            'attendances' => $attendances,
            'statistics' => [
                'total_records' => $totalRecords,
                'present_count' => $presentCount,
                'absent_count' => $absentCount,
                'late_count' => $lateCount,
                'excused_count' => $excusedCount,
                'present_rate' => $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 2) : 0,
            ],
        ]);
    }

    private function showGradeDistribution($report, $parameters)
    {
        $query = Grade::with(['student.user', 'classModel', 'assessment']);

        if (!empty($parameters['class_id'])) {
            $query->where('class_id', $parameters['class_id']);
        }

        if (!empty($parameters['assessment_type'])) {
            $query->where('assessment_type', $parameters['assessment_type']);
        }

        $grades = $query->get();

        $distribution = [
            'A' => $grades->where('letter_grade', 'A')->count(),
            'B' => $grades->where('letter_grade', 'B')->count(),
            'C' => $grades->where('letter_grade', 'C')->count(),
            'D' => $grades->where('letter_grade', 'D')->count(),
            'F' => $grades->where('letter_grade', 'F')->count(),
        ];

        $averageScore = $grades->avg('percentage');
        $highestScore = $grades->max('percentage');
        $lowestScore = $grades->min('percentage');

        return Inertia::render('Admin/Report/ViewGradeDistribution', [
            'report' => $report,
            'grades' => $grades,
            'distribution' => $distribution,
            'statistics' => [
                'total_grades' => $grades->count(),
                'average_score' => round($averageScore ?? 0, 2),
                'highest_score' => round($highestScore ?? 0, 2),
                'lowest_score' => round($lowestScore ?? 0, 2),
            ],
        ]);
    }

    private function showTeacherWorkload($report, $parameters)
    {
        $query = Teacher::with(['user', 'classes']);

        if (!empty($parameters['teacher_id'])) {
            $query->where('id', $parameters['teacher_id']);
        }

        $teachers = $query->where('status', 'active')->get();

        $workloadData = [];
        foreach ($teachers as $teacher) {
            $classes = $teacher->classes()->where('status', 'active')->get();
            $totalStudents = 0;
            foreach ($classes as $class) {
                $totalStudents += $class->enrollments()->where('status', 'active')->count();
            }

            $workloadData[] = [
                'teacher' => $teacher,
                'total_classes' => $classes->count(),
                'total_students' => $totalStudents,
                'classes' => $classes,
            ];
        }

        return Inertia::render('Admin/Report/ViewTeacherWorkload', [
            'report' => $report,
            'workloadData' => $workloadData,
        ]);
    }

    private function showEnrollment($report, $parameters)
    {
        $query = Enrollment::with(['student.user', 'classModel'])->where('status', 'active');

        if (!empty($parameters['academic_year'])) {
            $query->whereHas('classModel', function ($q) use ($parameters) {
                $q->where('academic_year', $parameters['academic_year']);
            });
        }

        if (!empty($parameters['class_id'])) {
            $query->where('class_id', $parameters['class_id']);
        }

        $enrollments = $query->get();

        $classStats = [];
        $enrollments->groupBy('class_id')->each(function ($classEnrollments, $classId) use (&$classStats) {
            $class = $classEnrollments->first()->classModel;
            $classStats[] = [
                'class' => $class,
                'total_students' => $classEnrollments->count(),
                'max_students' => $class->max_students ?? 0,
                'capacity_percentage' => $class->max_students > 0 
                    ? round(($classEnrollments->count() / $class->max_students) * 100, 2) 
                    : 0,
            ];
        });

        return Inertia::render('Admin/Report/ViewEnrollment', [
            'report' => $report,
            'enrollments' => $enrollments,
            'classStats' => $classStats,
            'totalEnrollments' => $enrollments->count(),
        ]);
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        FlashMessage::success('Report deleted successfully.');
        return redirect()->route('admin.reports.index');
    }
}

