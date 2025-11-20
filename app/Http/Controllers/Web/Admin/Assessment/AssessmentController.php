<?php

namespace App\Http\Controllers\Web\Admin\Assessment;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\ClassModel;
use App\Support\Service\FlashMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Assessment::with('classModel', 'createdBy');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('assessment_name', 'like', "%{$search}%")
                  ->orWhereHas('classModel', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('assessment_type')) {
            $query->where('assessment_type', $request->assessment_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $assessments = $query->latest('assessment_date')->paginate(15);

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Assessment/Index', [
            'assessments' => $assessments,
            'classes' => $classes,
            'filters' => $request->only(['search', 'class_id', 'assessment_type', 'status']),
        ]);
    }

    public function show($id)
    {
        $assessment = Assessment::with('classModel', 'createdBy', 'grades.student.user')
            ->findOrFail($id);

        return Inertia::render('Admin/Assessment/Show', [
            'assessment' => $assessment,
        ]);
    }

    public function destroy($id)
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->delete();

        FlashMessage::success('Assessment deleted successfully.');

        return redirect()->route('admin.assessments.index');
    }
}
