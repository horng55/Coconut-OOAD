<?php

namespace App\Http\Controllers\Web\Admin\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use App\Support\Service\FlashMessage;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('employee_id', 'like', "%{$search}%");
        }

        $teachers = $query->latest()->paginate(15);

        return Inertia::render('Admin/Teacher/Index', [
            'teachers' => $teachers,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Teacher/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'employee_id' => 'required|string|unique:teachers,employee_id',
            'subject_specialization' => 'required|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => 'teacher',
            'status' => 'active',
            'verified' => true,
            'gender' => $validated['gender'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        Teacher::create([
            'user_id' => $user->id,
            'employee_id' => $validated['employee_id'],
            'subject_specialization' => $validated['subject_specialization'],
            'qualification' => $validated['qualification'] ?? null,
            'hire_date' => $validated['hire_date'],
            'status' => 'active',
        ]);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher created successfully.');
    }

    public function show($id)
    {
        $teacher = Teacher::with('user', 'classes')->findOrFail($id);
        return Inertia::render('Admin/Teacher/Show', [
            'teacher' => $teacher,
        ]);
    }

    public function edit($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        return Inertia::render('Admin/Teacher/Edit', [
            'teacher' => $teacher,
        ]);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $teacher->user_id,
            'email' => 'required|email|unique:users,email,' . $teacher->user_id,
            'password' => 'nullable|string|min:8',
            'employee_id' => 'required|string|unique:teachers,employee_id,' . $id,
            'subject_specialization' => 'required|string|max:255',
            'qualification' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'gender' => 'nullable|in:male,female,other',
            'phone_number' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $teacher->user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'gender' => $validated['gender'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        if (!empty($validated['password'])) {
            $teacher->user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $teacher->update([
            'employee_id' => $validated['employee_id'],
            'subject_specialization' => $validated['subject_specialization'],
            'qualification' => $validated['qualification'] ?? null,
            'hire_date' => $validated['hire_date'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $teacher = Teacher::with('user', 'classes')->findOrFail($id);
            
            // Check if teacher has classes assigned
            if ($teacher->classes && $teacher->classes->count() > 0) {
                FlashMessage::error('Cannot delete teacher. This teacher is assigned to one or more classes. Please reassign or remove the classes first.');
                return redirect()->route('admin.teachers.index');
            }

            // Store user_id before deleting teacher
            $userId = $teacher->user_id;
            
            // Delete teacher first (this will cascade or set null on classes due to foreign key)
            $teacher->delete();
            
            // Then delete the associated user
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->delete();
                }
            }

            FlashMessage::success('Teacher deleted successfully.');
            return redirect()->route('admin.teachers.index');
        } catch (\Exception $e) {
            FlashMessage::error('Failed to delete teacher: ' . $e->getMessage());
            return redirect()->route('admin.teachers.index');
        }
    }
}
