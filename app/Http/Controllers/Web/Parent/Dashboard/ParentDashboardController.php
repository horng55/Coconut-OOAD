<?php

namespace App\Http\Controllers\Web\Parent\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ParentUser;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Announcement;
use App\Models\FeePayment;
use Inertia\Inertia;

class ParentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $parent = ParentUser::where('user_id', $user->id)
            ->with(['user', 'students.user', 'students.enrollments.classModel.teachers.user'])
            ->first();

        if (!$parent) {
            Auth::logout();
            return redirect()->route('parent.login')
                ->withErrors(['email' => 'Parent record not found.']);
        }

        // Get all children's enrollments
        $studentIds = $parent->students->pluck('id')->toArray();
        
        $enrollments = Enrollment::whereIn('student_id', $studentIds)
            ->where('status', 'active')
            ->with(['student.user', 'classModel.teachers.user'])
            ->latest()
            ->get();

        // Get all children's recent grades (last 15)
        $recentGrades = Grade::whereIn('student_id', $studentIds)
            ->with(['student.user', 'classModel'])
            ->latest('assessment_date')
            ->limit(15)
            ->get();

        // Get all children's recent attendance (last 15)
        $recentAttendance = Attendance::whereIn('student_id', $studentIds)
            ->with(['student.user', 'classModel'])
            ->latest('date')
            ->limit(15)
            ->get();

        // Get class announcements for all enrolled classes
        $classIds = $enrollments->pluck('class_id')->unique()->toArray();
        $announcements = Announcement::whereIn('class_id', $classIds)
            ->with('classModel')
            ->latest()
            ->limit(10)
            ->get();

        // Get fee payments for all children
        $feePayments = FeePayment::whereIn('student_id', $studentIds)
            ->with(['fee', 'student.user'])
            ->latest('due_date')
            ->limit(10)
            ->get();

        // Get pending/overdue payments
        $pendingPayments = FeePayment::whereIn('student_id', $studentIds)
            ->whereIn('status', ['pending', 'overdue', 'partial'])
            ->with(['fee', 'student.user'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        // Calculate statistics across all children
        $totalFeeAmount = FeePayment::whereIn('student_id', $studentIds)->sum('amount');
        $totalPaid = FeePayment::whereIn('student_id', $studentIds)->where('status', 'paid')->sum('amount_paid');
        $totalPending = FeePayment::whereIn('student_id', $studentIds)
            ->whereIn('status', ['pending', 'overdue', 'partial'])
            ->sum('remaining_amount');

        $stats = [
            'total_children' => $parent->students->count(),
            'total_classes' => $enrollments->count(),
            'total_grades' => Grade::whereIn('student_id', $studentIds)->count(),
            'present_count' => Attendance::whereIn('student_id', $studentIds)->where('status', 'present')->count(),
            'total_attendance' => Attendance::whereIn('student_id', $studentIds)->count(),
            'total_fee_amount' => $totalFeeAmount,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'pending_payments_count' => FeePayment::whereIn('student_id', $studentIds)
                ->whereIn('status', ['pending', 'overdue', 'partial'])
                ->count(),
        ];

        return Inertia::render('Parent/Dashboard', [
            'parent' => $parent,
            'children' => $parent->students->load(['enrollments.classModel', 'grades', 'attendances']),
            'enrollments' => $enrollments,
            'recentGrades' => $recentGrades,
            'recentAttendance' => $recentAttendance,
            'announcements' => $announcements,
            'feePayments' => $feePayments,
            'pendingPayments' => $pendingPayments,
            'stats' => $stats,
        ]);
    }
}
