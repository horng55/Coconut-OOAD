<?php

namespace App\Http\Controllers\Web\Student\Announcement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Announcement;
use Inertia\Inertia;

class StudentAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.login')
                ->withErrors(['email' => 'Student record not found.']);
        }

        // Get student's enrolled class IDs
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id')
            ->toArray();

        $query = Announcement::whereIn('class_id', $classIds)
            ->orWhere(function ($q) {
                $q->whereJsonContains('target_audience', 'students')
                  ->orWhereJsonContains('target_audience', 'all');
            })
            ->with('classModel', 'createdByUser');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
        }

        if ($request->has('class_id') && in_array($request->class_id, $classIds)) {
            $query->where('class_id', $request->class_id);
        }

        $announcements = $query->latest('publish_date')->paginate(15);

        // Get classes for filter
        $classes = \App\Models\ClassModel::whereIn('id', $classIds)
            ->where('status', 'active')
            ->get(['id', 'name', 'code']);

        return Inertia::render('Student/Announcement/Index', [
            'announcements' => $announcements,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id']),
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.login')
                ->withErrors(['email' => 'Student record not found.']);
        }

        // Get student's enrolled class IDs
        $classIds = $student->enrollments()
            ->where('status', 'active')
            ->pluck('class_id')
            ->toArray();

        $announcement = Announcement::where('id', $id)
            ->where(function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds)
                  ->orWhere(function ($query) {
                      $query->whereJsonContains('target_audience', 'students')
                            ->orWhereJsonContains('target_audience', 'all');
                  });
            })
            ->with('classModel', 'createdByUser')
            ->firstOrFail();

        return Inertia::render('Student/Announcement/Show', [
            'announcement' => $announcement,
        ]);
    }
}
