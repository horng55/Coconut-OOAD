<?php

namespace App\Http\Controllers\Web\Teacher\Class;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Teacher;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;
use Inertia\Inertia;

class TeacherClassController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $query = $teacher->classes()
            ->select('classes.*')
            ->where('classes.status', 'active')
            ->with(['teachers.user', 'subjects', 'enrollments.student.user']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('classes.name', 'like', "%{$search}%")
                  ->orWhere('classes.code', 'like', "%{$search}%");
        }

        if ($request->has('academic_year')) {
            $query->where('classes.academic_year', $request->academic_year);
        }

        $classes = $query->latest('classes.created_at')->paginate(15);

        return Inertia::render('Teacher/Class/Index', [
            'classes' => $classes,
            'filters' => $request->only(['search', 'academic_year']),
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->route('teacher.login')
                ->withErrors(['email' => 'Teacher record not found.']);
        }

        $class = $teacher->classes()
            ->select('classes.*')
            ->where('classes.id', $id)
            ->where('classes.status', 'active')
            ->with(['teachers.user', 'subjects', 'enrollments.student.user'])
            ->firstOrFail();

        // Get assessments for this class
        $assessments = $class->assessments()
            ->where('created_by', $user->id)
            ->where('status', 'active')
            ->with(['grades'])
            ->orderBy('assessment_date', 'desc')
            ->limit(5)
            ->get();

        // Get statistics
        $stats = [
            'total_students' => $class->enrollments()->where('status', 'active')->count(),
            'total_subjects' => $class->subjects()->count(),
            'total_teachers' => $class->teachers()->count(),
            'total_assessments' => $class->assessments()->where('created_by', $user->id)->count(),
        ];

        return Inertia::render('Teacher/Class/Show', [
            'classItem' => $class,
            'stats' => $stats,
            'assessments' => $assessments,
        ]);
    }

    public function create()
    {
        $subjects = Subject::where('status', 'active')
            ->select('id', 'name', 'code')
            ->orderBy('name')
            ->get();

        return Inertia::render('Teacher/Class/Create', [
            'subjects' => $subjects,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return back()->withErrors(['error' => 'Teacher record not found.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:classes,code',
            'description' => 'nullable|string',
            'academic_year' => 'required|string|max:50',
            'max_students' => 'required|integer|min:1|max:100',
            'subject_ids' => 'nullable|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        DB::beginTransaction();
        try {
            $class = ClassModel::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'],
                'academic_year' => $validated['academic_year'],
                'max_students' => $validated['max_students'],
                'teacher_id' => $teacher->id,
                'status' => 'active',
            ]);

            // Attach the teacher to the class
            $class->teachers()->attach($teacher->id);

            // Attach subjects if provided
            if (!empty($validated['subject_ids'])) {
                $class->subjects()->attach($validated['subject_ids']);
            }

            DB::commit();

            return redirect()->route('teacher.classes.show', $class->id)
                ->with('alert', [
                    'type' => 'success',
                    'message' => 'Class created successfully!'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create class: ' . $e->getMessage()]);
        }
    }

    public function createStudent($classId)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return back()->withErrors(['error' => 'Teacher record not found.']);
        }

        $class = $teacher->classes()
            ->where('classes.id', $classId)
            ->firstOrFail();

        return Inertia::render('Teacher/Class/CreateStudent', [
            'classItem' => $class,
        ]);
    }

    public function storeStudent(Request $request, $classId)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return back()->withErrors(['error' => 'Teacher record not found.']);
        }

        $class = $teacher->classes()
            ->where('classes.id', $classId)
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'address' => 'nullable|string',
            'student_id' => 'required|string|unique:students,student_id',
            'admission_date' => 'required|date',
            'medical_info' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $userAccount = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make('password123'), // Default password
                'phone' => $validated['phone'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'type' => 'student',
                'status' => 'active',
            ]);

            // Create student record
            $student = Student::create([
                'user_id' => $userAccount->id,
                'student_id' => $validated['student_id'],
                'admission_date' => $validated['admission_date'],
                'medical_info' => $validated['medical_info'],
                'status' => 'active',
            ]);

            // Enroll in class
            Enrollment::create([
                'student_id' => $student->id,
                'class_id' => $classId,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('teacher.classes.show', $classId)
                ->with('alert', [
                    'type' => 'success',
                    'message' => 'Student created and enrolled successfully! Default password: password123'
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create student: ' . $e->getMessage()]);
        }
    }

    public function enrollStudent($classId)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return back()->withErrors(['error' => 'Teacher record not found.']);
        }

        $class = $teacher->classes()
            ->where('classes.id', $classId)
            ->with('enrollments')
            ->firstOrFail();

        // Get enrolled student IDs
        $enrolledStudentIds = $class->enrollments->pluck('student_id')->toArray();

        // Get students not yet enrolled in this class
        $availableStudents = Student::where('status', 'active')
            ->whereNotIn('id', $enrolledStudentIds)
            ->with('user')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'name' => $student->user->full_name,
                    'email' => $student->user->email,
                ];
            });

        return Inertia::render('Teacher/Class/EnrollStudent', [
            'classItem' => $class,
            'availableStudents' => $availableStudents,
        ]);
    }

    public function storeEnrollment(Request $request, $classId)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return back()->withErrors(['error' => 'Teacher record not found.']);
        }

        $class = $teacher->classes()
            ->where('classes.id', $classId)
            ->firstOrFail();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_id', $classId)
            ->first();

        if ($existingEnrollment) {
            return back()->withErrors(['error' => 'Student is already enrolled in this class.']);
        }

        try {
            Enrollment::create([
                'student_id' => $validated['student_id'],
                'class_id' => $classId,
                'enrollment_date' => now(),
                'status' => 'active',
            ]);

            return redirect()->route('teacher.classes.show', $classId)
                ->with('alert', [
                    'type' => 'success',
                    'message' => 'Student enrolled successfully!'
                ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to enroll student: ' . $e->getMessage()]);
        }
    }
}
