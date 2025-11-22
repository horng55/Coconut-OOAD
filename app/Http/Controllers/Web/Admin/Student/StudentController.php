<?php

namespace App\Http\Controllers\Web\Admin\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ParentUser;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('user', 'parent.user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('student_id', 'like', "%{$search}%");
        }

        $students = $query->latest()->paginate(15);

        return Inertia::render('Admin/Student/Index', [
            'students' => $students,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $classes = ClassModel::where('status', 'active')
            ->select('id', 'name', 'code', 'academic_year')
            ->orderBy('name')
            ->get();

        $teachers = Teacher::where('status', 'active')
            ->with('user:id,first_name,last_name,email')
            ->get()
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->user->full_name,
                    'email' => $teacher->user->email,
                    'employee_id' => $teacher->employee_id,
                    'subject_specialization' => $teacher->subject_specialization,
                ];
            });

        return Inertia::render('Admin/Student/Create', [
            'classes' => $classes,
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'student_id' => 'required|string|unique:students,student_id',
            'admission_date' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'phone_number' => 'nullable|string|max:20',
            'medical_info' => 'nullable|string',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'type' => 'student',
                'status' => 'active',
                'verified' => true,
                'gender' => $validated['gender'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'student_id' => $validated['student_id'],
                'admission_date' => $validated['admission_date'],
                'status' => 'active',
                'medical_info' => $validated['medical_info'] ?? null,
            ]);

            // Create enrollments if classes selected
            if (!empty($validated['class_ids'])) {
                foreach ($validated['class_ids'] as $classId) {
                    Enrollment::create([
                        'student_id' => $student->id,
                        'class_id' => $classId,
                        'enrollment_date' => now(),
                        'status' => 'active',
                    ]);
                }
            }

            // Assign teachers if selected
            if (!empty($validated['teacher_ids'])) {
                // Get classes for the teachers to create proper relationships
                $teacherClasses = DB::table('class_teacher')
                    ->whereIn('teacher_id', $validated['teacher_ids'])
                    ->pluck('class_id', 'teacher_id');

                foreach ($validated['teacher_ids'] as $teacherId) {
                    // Only create enrollment if there's a class associated with this teacher
                    // and the student isn't already enrolled
                    if (isset($teacherClasses[$teacherId])) {
                        $classId = $teacherClasses[$teacherId];
                        $exists = Enrollment::where('student_id', $student->id)
                            ->where('class_id', $classId)
                            ->exists();

                        if (!$exists) {
                            Enrollment::create([
                                'student_id' => $student->id,
                                'class_id' => $classId,
                                'enrollment_date' => now(),
                                'status' => 'active',
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create student: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $student = Student::with([
            'user', 
            'parent.user', 
            'enrollments.classModel', 
            'grades.classModel',
            'grades.assessment',
            'attendances' => function ($query) {
                $query->latest('date')->limit(6)
                    ->with(['classModel' => function ($q) {
                        $q->select('id', 'name', 'code');
                    }]);
            }
        ])
        ->findOrFail($id);
        
        return Inertia::render('Admin/Student/Show', [
            'student' => $student,
        ]);
    }

    public function edit($id)
    {
        $student = Student::with(['user', 'enrollments'])->findOrFail($id);
        
        $classes = ClassModel::where('status', 'active')
            ->select('id', 'name', 'code', 'academic_year')
            ->orderBy('name')
            ->get();
        
        // Get currently enrolled class IDs
        $enrolledClassIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id')
            ->toArray();

        return Inertia::render('Admin/Student/Edit', [
            'student' => $student,
            'classes' => $classes,
            'enrolledClassIds' => $enrolledClassIds,
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $student->user_id,
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'password' => 'nullable|string|min:8',
            'student_id' => 'required|string|unique:students,student_id,' . $id,
            'admission_date' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'phone_number' => 'nullable|string|max:20',
            'medical_info' => 'nullable|string',
            'status' => 'required|in:active,inactive,graduated,transferred',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
        ]);

        DB::beginTransaction();
        try {
            $student->user->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'gender' => $validated['gender'] ?? null,
                'phone_number' => $validated['phone_number'] ?? null,
            ]);

            if (!empty($validated['password'])) {
                $student->user->update([
                    'password' => Hash::make($validated['password']),
                ]);
            }

            $student->update([
                'student_id' => $validated['student_id'],
                'admission_date' => $validated['admission_date'],
                'status' => $validated['status'],
                'medical_info' => $validated['medical_info'] ?? null,
            ]);

            // Handle class enrollments
            if (isset($validated['class_ids'])) {
                // Get current active enrollments
                $currentEnrollments = $student->enrollments()
                    ->where('status', 'active')
                    ->pluck('class_id')
                    ->toArray();

                $newClassIds = $validated['class_ids'];

                // Classes to remove (in current but not in new)
                $classesToRemove = array_diff($currentEnrollments, $newClassIds);
                
                // Classes to add (in new but not in current)
                $classesToAdd = array_diff($newClassIds, $currentEnrollments);

                // Remove enrollments
                if (!empty($classesToRemove)) {
                    Enrollment::where('student_id', $student->id)
                        ->whereIn('class_id', $classesToRemove)
                        ->delete();
                }

                // Add new enrollments
                foreach ($classesToAdd as $classId) {
                    Enrollment::create([
                        'student_id' => $student->id,
                        'class_id' => $classId,
                        'enrollment_date' => now(),
                        'status' => 'active',
                    ]);
                }
            } else {
                // If no classes selected, remove all enrollments
                Enrollment::where('student_id', $student->id)
                    ->where('status', 'active')
                    ->delete();
            }

            DB::commit();

            return redirect()->route('admin.students.index')
                ->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update student: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $student = Student::with('user')->findOrFail($id);
        
        // Store user reference before deleting student
        $user = $student->user;
        
        // Delete student first (soft delete)
        $student->delete();
        
        // Delete user if it exists
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }
}
