<?php

namespace App\Http\Controllers\Web\Admin\IdCard;

use App\Http\Controllers\Controller;
use App\Models\IdCard;
use App\Models\Student;
use App\Models\Teacher;
use App\Support\Service\FlashMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class IdCardController extends Controller
{
    public function index(Request $request)
    {
        $query = IdCard::with(['student.user', 'teacher.user']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('card_number', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('teacher.user', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        if ($request->has('card_type')) {
            $query->where('card_type', $request->card_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $idCards = $query->latest()->paginate(15);

        return Inertia::render('Admin/IdCard/Index', [
            'idCards' => $idCards,
            'filters' => $request->only(['search', 'card_type', 'status']),
        ]);
    }

    public function create(Request $request)
    {
        $cardType = $request->get('type', 'student'); // Default to student

        // Always load both students and teachers so the dropdown works when switching types
        $students = Student::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                    'student_id' => $student->student_id,
                    'email' => $student->user->email,
                ];
            });

        $teachers = Teacher::with('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->user->full_name,
                    'employee_id' => $teacher->employee_id,
                    'email' => $teacher->user->email,
                ];
            });

        return Inertia::render('Admin/IdCard/Create', [
            'cardType' => $cardType,
            'students' => $students,
            'teachers' => $teachers,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'card_type' => 'required|in:student,teacher',
            'student_id' => 'required_if:card_type,student|nullable|exists:students,id',
            'teacher_id' => 'required_if:card_type,teacher|nullable|exists:teachers,id',
            'card_number' => 'required|string|max:255|unique:id_cards,card_number',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'required|in:active,expired,revoked',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('id-cards', 'public');
        }

        IdCard::create([
            'card_type' => $validated['card_type'],
            'student_id' => $validated['card_type'] === 'student' ? $validated['student_id'] : null,
            'teacher_id' => $validated['card_type'] === 'teacher' ? $validated['teacher_id'] : null,
            'card_number' => $validated['card_number'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'status' => $validated['status'],
            'photo_path' => $photoPath,
            'notes' => $validated['notes'] ?? null,
        ]);

        FlashMessage::success('ID Card created successfully.');
        return redirect()->route('admin.id-cards.index');
    }

    public function show($id)
    {
        $idCard = IdCard::with(['student.user', 'teacher.user'])->findOrFail($id);

        return Inertia::render('Admin/IdCard/Show', [
            'idCard' => $idCard,
        ]);
    }

    public function edit($id)
    {
        $idCard = IdCard::with(['student.user', 'teacher.user'])->findOrFail($id);

        $students = [];
        $teachers = [];

        if ($idCard->card_type === 'student') {
            $students = Student::with('user')
                ->where('status', 'active')
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->user->full_name,
                        'student_id' => $student->student_id,
                        'email' => $student->user->email,
                    ];
                });
        } else {
            $teachers = Teacher::with('user')
                ->where('status', 'active')
                ->get()
                ->map(function ($teacher) {
                    return [
                        'id' => $teacher->id,
                        'name' => $teacher->user->full_name,
                        'employee_id' => $teacher->employee_id,
                        'email' => $teacher->user->email,
                    ];
                });
        }

        return Inertia::render('Admin/IdCard/Edit', [
            'idCard' => $idCard,
            'students' => $students,
            'teachers' => $teachers,
        ]);
    }

    public function update(Request $request, $id)
    {
        $idCard = IdCard::findOrFail($id);

        $validated = $request->validate([
            'card_type' => 'required|in:student,teacher',
            'student_id' => 'required_if:card_type,student|nullable|exists:students,id',
            'teacher_id' => 'required_if:card_type,teacher|nullable|exists:teachers,id',
            'card_number' => 'required|string|max:255|unique:id_cards,card_number,' . $id,
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'status' => 'required|in:active,expired,revoked',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($idCard->photo_path) {
                Storage::disk('public')->delete($idCard->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('id-cards', 'public');
        }

        $idCard->update([
            'card_type' => $validated['card_type'],
            'student_id' => $validated['card_type'] === 'student' ? $validated['student_id'] : null,
            'teacher_id' => $validated['card_type'] === 'teacher' ? $validated['teacher_id'] : null,
            'card_number' => $validated['card_number'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'] ?? null,
            'status' => $validated['status'],
            'photo_path' => $validated['photo_path'] ?? $idCard->photo_path,
            'notes' => $validated['notes'] ?? null,
        ]);

        FlashMessage::success('ID Card updated successfully.');
        return redirect()->route('admin.id-cards.index');
    }

    public function destroy($id)
    {
        $idCard = IdCard::findOrFail($id);

        // Delete photo if exists
        if ($idCard->photo_path) {
            Storage::disk('public')->delete($idCard->photo_path);
        }

        $idCard->delete();

        FlashMessage::success('ID Card deleted successfully.');
        return redirect()->route('admin.id-cards.index');
    }
}
