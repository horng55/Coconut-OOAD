<?php

namespace App\Http\Controllers\Web\Admin\Parent;

use App\Http\Controllers\Controller;
use App\Models\ParentUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $query = ParentUser::with('user', 'students.user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $parents = $query->latest()->paginate(15);

        return Inertia::render('Admin/Parent/Index', [
            'parents' => $parents,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Parent/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'relationship' => 'required|in:father,mother,guardian',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => 'parent',
            'status' => 'active',
            'verified' => true,
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        ParentUser::create([
            'user_id' => $user->id,
            'relationship' => $validated['relationship'],
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ? ['phone' => $validated['emergency_contact']] : null,
        ]);

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent created successfully.');
    }

    public function show($id)
    {
        $parent = ParentUser::with('user', 'students.user', 'students.enrollments.classModel')
            ->findOrFail($id);
        return Inertia::render('Admin/Parent/Show', [
            'parent' => $parent,
        ]);
    }

    public function edit($id)
    {
        $parent = ParentUser::with('user')->findOrFail($id);
        return Inertia::render('Admin/Parent/Edit', [
            'parent' => $parent,
        ]);
    }

    public function update(Request $request, $id)
    {
        $parent = ParentUser::with('user')->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $parent->user_id,
            'email' => 'required|email|unique:users,email,' . $parent->user_id,
            'password' => 'nullable|string|min:8',
            'relationship' => 'required|in:father,mother,guardian',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'status' => 'required|in:active,disable',
        ]);

        $parent->user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'status' => $validated['status'],
        ]);

        if (!empty($validated['password'])) {
            $parent->user->update([
                'password' => Hash::make($validated['password']),
            ]);
        }

        $parent->update([
            'relationship' => $validated['relationship'],
            'address' => $validated['address'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ? ['phone' => $validated['emergency_contact']] : null,
        ]);

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent updated successfully.');
    }

    public function destroy($id)
    {
        $parent = ParentUser::with('user')->findOrFail($id);
        
        // Store user reference before deleting parent
        $user = $parent->user;
        
        // Delete parent first (soft delete)
        $parent->delete();
        
        // Delete user if it exists
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.parents.index')
            ->with('success', 'Parent deleted successfully.');
    }
}
