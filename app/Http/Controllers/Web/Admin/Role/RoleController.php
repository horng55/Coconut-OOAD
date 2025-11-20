<?php

namespace App\Http\Controllers\Web\Admin\Role;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $roles = $query->latest()->paginate(15);

        return Inertia::render('Admin/Role/Index', [
            'roles' => $roles,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Role/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Role::create($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show($id)
    {
        $role = Role::withCount('users')->with('users')->findOrFail($id);
        
        return Inertia::render('Admin/Role/Show', [
            'role' => $role,
        ]);
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);

        return Inertia::render('Admin/Role/Edit', [
            'role' => $role,
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $role->update($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete role. There are users assigned to this role.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
