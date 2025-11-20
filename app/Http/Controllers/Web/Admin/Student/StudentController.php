<?php

namespace App\Http\Controllers\Web\Admin\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\ParentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $parents = ParentUser::with('user')->get()->map(function ($parent) {
            return [
                'id' => $parent->id,
                'name' => $parent->user->full_name,
                'email' => $parent->user->email,
            ];
        });

        return Inertia::render('Admin/Student/Create', [
            'parents' => $parents,
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
            'parent_id' => 'nullable|exists:parents,id',
            'admission_date' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'phone_number' => 'nullable|string|max:20',
            'medical_info' => 'nullable|string',
        ]);

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

        Student::create([
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'student_id' => $validated['student_id'],
            'admission_date' => $validated['admission_date'],
            'status' => 'active',
            'medical_info' => $validated['medical_info'] ?? null,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully.');
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
        $student = Student::with('user', 'parent')->findOrFail($id);
        $parents = ParentUser::with('user')->get()->map(function ($parent) {
            return [
                'id' => $parent->id,
                'name' => $parent->user->full_name,
                'email' => $parent->user->email,
            ];
        });

        return Inertia::render('Admin/Student/Edit', [
            'student' => $student,
            'parents' => $parents,
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
            'parent_id' => 'nullable|exists:parents,id',
            'admission_date' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'phone_number' => 'nullable|string|max:20',
            'medical_info' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

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
            'parent_id' => $validated['parent_id'] ?? null,
            'student_id' => $validated['student_id'],
            'admission_date' => $validated['admission_date'],
            'status' => $validated['status'],
            'medical_info' => $validated['medical_info'] ?? null,
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
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
