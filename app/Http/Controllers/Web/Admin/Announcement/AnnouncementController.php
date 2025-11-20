<?php

namespace App\Http\Controllers\Web\Admin\Announcement;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = Announcement::with('classModel', 'createdByUser');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('target_audience') && $request->target_audience) {
            $query->whereJsonContains('target_audience', $request->target_audience);
        }

        $announcements = $query->latest('publish_date')->paginate(15);

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Announcement/Index', [
            'announcements' => $announcements,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'target_audience']),
        ]);
    }

    public function create()
    {
        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Announcement/Create', [
            'classes' => $classes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|array|min:1',
            'target_audience.*' => 'required|in:all,teachers,students,parents',
            'class_id' => 'nullable|exists:classes,id',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:publish_date',
            'is_pinned' => 'boolean',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        // If 'all' is selected, only store 'all', otherwise store the selected portals
        $targetAudience = in_array('all', $validated['target_audience']) 
            ? ['all'] 
            : array_values(array_unique($validated['target_audience']));

        Announcement::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'target_audience' => $targetAudience,
            'class_id' => $validated['class_id'] ?? null,
            'created_by' => Auth::id(),
            'publish_date' => $validated['publish_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'is_pinned' => $validated['is_pinned'] ?? false,
            'priority' => $validated['priority'],
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function show($id)
    {
        $announcement = Announcement::with('classModel', 'createdByUser')
            ->findOrFail($id);
        return Inertia::render('Admin/Announcement/Show', [
            'announcement' => $announcement,
        ]);
    }

    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Announcement/Edit', [
            'announcement' => $announcement,
            'classes' => $classes,
        ]);
    }

    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|array|min:1',
            'target_audience.*' => 'required|in:all,teachers,students,parents',
            'class_id' => 'nullable|exists:classes,id',
            'publish_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:publish_date',
            'is_pinned' => 'boolean',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        // If 'all' is selected, only store 'all', otherwise store the selected portals
        $targetAudience = in_array('all', $validated['target_audience']) 
            ? ['all'] 
            : array_values(array_unique($validated['target_audience']));

        $announcement->update(array_merge($validated, ['target_audience' => $targetAudience]));

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }
}
