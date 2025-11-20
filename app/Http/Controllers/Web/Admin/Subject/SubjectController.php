<?php

namespace App\Http\Controllers\Web\Admin\Subject;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::withCount('classes');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $subjects = $query->latest()->paginate(15);

        return Inertia::render('Admin/Subject/Index', [
            'subjects' => $subjects,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Subject/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:subjects,code',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function show($id)
    {
        $subject = Subject::with('classes')->findOrFail($id);
        
        return Inertia::render('Admin/Subject/Show', [
            'subject' => $subject,
        ]);
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);

        return Inertia::render('Admin/Subject/Edit', [
            'subject' => $subject,
        ]);
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:subjects,code,' . $id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
