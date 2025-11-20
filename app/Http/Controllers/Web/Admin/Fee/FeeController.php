<?php

namespace App\Http\Controllers\Web\Admin\Fee;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\FeePayment;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Fee::with(['classModel', 'payments'])->withCount('payments');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('classModel', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  });
        }

        if ($request->has('fee_type')) {
            $query->where('fee_type', $request->fee_type);
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $fees = $query->latest()->paginate(15);

        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);
        $currentYear = date('Y');
        $defaultAcademicYear = ($currentYear - 1) . '-' . $currentYear;

        return Inertia::render('Admin/Fee/Index', [
            'fees' => $fees,
            'classes' => $classes,
            'filters' => $request->only(['search', 'fee_type', 'class_id', 'academic_year', 'status']),
            'defaultAcademicYear' => $defaultAcademicYear,
        ]);
    }

    public function create()
    {
        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);
        $currentYear = date('Y');
        $defaultAcademicYear = ($currentYear - 1) . '-' . $currentYear;

        return Inertia::render('Admin/Fee/Create', [
            'classes' => $classes,
            'defaultAcademicYear' => $defaultAcademicYear,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fee_type' => 'required|in:tuition,library,sports,exam,transport,hostel,other',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'class_id' => 'nullable|exists:classes,id',
            'academic_year' => 'required|string',
            'due_date' => 'nullable|date',
            'is_recurring' => 'boolean',
            'recurring_period' => 'nullable|in:monthly,quarterly,semester,yearly',
            'status' => 'required|in:active,inactive',
        ]);

        Fee::create($validated);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee created successfully.');
    }

    public function show($id)
    {
        $fee = Fee::with(['classModel', 'payments.student.user', 'payments.paidBy'])
            ->withCount('payments')
            ->findOrFail($id);

        // Get statistics
        $stats = [
            'total_payments' => $fee->payments()->count(),
            'paid_count' => $fee->payments()->where('status', 'paid')->count(),
            'pending_count' => $fee->payments()->where('status', 'pending')->count(),
            'overdue_count' => $fee->payments()->where('status', 'overdue')->count(),
            'total_collected' => $fee->payments()->where('status', 'paid')->sum('amount_paid'),
            'total_pending' => $fee->payments()->whereIn('status', ['pending', 'overdue'])->sum('remaining_amount'),
        ];

        return Inertia::render('Admin/Fee/Show', [
            'fee' => $fee,
            'stats' => $stats,
        ]);
    }

    public function edit($id)
    {
        $fee = Fee::findOrFail($id);
        $classes = ClassModel::where('status', 'active')->get(['id', 'name', 'code']);

        return Inertia::render('Admin/Fee/Edit', [
            'fee' => $fee,
            'classes' => $classes,
        ]);
    }

    public function update(Request $request, $id)
    {
        $fee = Fee::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fee_type' => 'required|in:tuition,library,sports,exam,transport,hostel,other',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'class_id' => 'nullable|exists:classes,id',
            'academic_year' => 'required|string',
            'due_date' => 'nullable|date',
            'is_recurring' => 'boolean',
            'recurring_period' => 'nullable|in:monthly,quarterly,semester,yearly',
            'status' => 'required|in:active,inactive',
        ]);

        $fee->update($validated);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee updated successfully.');
    }

    public function destroy($id)
    {
        $fee = Fee::findOrFail($id);
        
        // Check if fee has payments
        if ($fee->payments()->count() > 0) {
            return redirect()->route('admin.fees.index')
                ->with('error', 'Cannot delete fee. There are payments associated with this fee.');
        }

        $fee->delete();

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee deleted successfully.');
    }

    public function createPayment($id)
    {
        $fee = Fee::with('classModel')->findOrFail($id);
        
        // Get students for this fee
        $students = [];
        if ($fee->class_id) {
            $students = Student::whereHas('enrollments', function ($q) use ($fee) {
                $q->where('class_id', $fee->class_id)->where('status', 'active');
            })
            ->with('user')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->full_name,
                    'student_id' => $student->student_id,
                ];
            });
        } else {
            // General fee - get all active students
            $students = Student::where('status', 'active')
                ->with('user')
                ->get()
                ->map(function ($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->user->full_name,
                        'student_id' => $student->student_id,
                    ];
                });
        }

        return Inertia::render('Admin/Fee/CreatePayment', [
            'fee' => $fee,
            'students' => $students,
        ]);
    }

    public function storePayment(Request $request, $id)
    {
        $fee = Fee::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'academic_year' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Calculate remaining amount (initially same as amount)
        $validated['fee_id'] = $fee->id;
        $validated['amount_paid'] = 0;
        $validated['remaining_amount'] = $validated['amount'];
        $validated['status'] = 'pending';

        FeePayment::create($validated);

        return redirect()->route('admin.fees.show', $fee->id)
            ->with('success', 'Fee payment record created successfully.');
    }

    public function recordPayment($feeId, $paymentId)
    {
        $fee = Fee::findOrFail($feeId);
        $feePayment = FeePayment::with(['student.user', 'fee'])->findOrFail($paymentId);

        return Inertia::render('Admin/Fee/RecordPayment', [
            'fee' => $fee,
            'feePayment' => $feePayment,
        ]);
    }

    public function updatePayment(Request $request, $feeId, $paymentId)
    {
        $feePayment = FeePayment::findOrFail($paymentId);

        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,card,online,cheque,other',
            'transaction_id' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:255|unique:fee_payments,receipt_number,' . $paymentId,
            'notes' => 'nullable|string',
        ]);

        // Calculate remaining amount
        $totalPaid = $feePayment->amount_paid + $validated['amount_paid'];
        $remaining = $feePayment->amount - $totalPaid;

        $validated['amount_paid'] = $totalPaid;
        $validated['remaining_amount'] = max(0, $remaining);
        $validated['status'] = $remaining <= 0 ? 'paid' : ($totalPaid > 0 ? 'partial' : 'pending');
        $validated['paid_by'] = Auth::id();

        // Generate receipt number if not provided
        if (empty($validated['receipt_number'])) {
            $validated['receipt_number'] = 'RCP-' . strtoupper(Str::random(8));
        }

        $feePayment->update($validated);

        return redirect()->route('admin.fees.show', $feeId)
            ->with('success', 'Payment recorded successfully.');
    }

    public function paymentsIndex(Request $request)
    {
        $query = FeePayment::with(['fee', 'student.user', 'paidBy']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhereHas('fee', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhere('receipt_number', 'like', "%{$search}%")
            ->orWhere('transaction_id', 'like', "%{$search}%");
        }

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('fee_id')) {
            $query->where('fee_id', $request->fee_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $perPage = $request->get('per_page', 10);
        $payments = $query->latest('created_at')->paginate($perPage);

        // Get statistics
        $stats = [
            'total_payments' => FeePayment::count(),
            'paid_count' => FeePayment::where('status', 'paid')->count(),
            'pending_count' => FeePayment::where('status', 'pending')->count(),
            'overdue_count' => FeePayment::where('status', 'overdue')->count(),
            'total_collected' => FeePayment::where('status', 'paid')->sum('amount_paid'),
            'total_pending' => FeePayment::whereIn('status', ['pending', 'overdue'])->sum('remaining_amount'),
        ];

        $fees = Fee::where('status', 'active')->get(['id', 'name', 'fee_type']);
        $students = Student::with('user')->get()->map(function ($student) {
            return [
                'id' => $student->id,
                'name' => $student->user->full_name,
            ];
        });

        return Inertia::render('Admin/Fee/PaymentsIndex', [
            'payments' => $payments,
            'fees' => $fees,
            'students' => $students,
            'stats' => $stats,
            'filters' => $request->only(['search', 'student_id', 'fee_id', 'status', 'academic_year', 'per_page']),
        ]);
    }

    public function showPayment($id)
    {
        $payment = FeePayment::with([
            'fee.classModel',
            'student.user',
            'paidBy'
        ])->findOrFail($id);

        // Add paid_by_user to payment object for easier access
        $payment->paid_by_user = $payment->paidBy;

        // Get payment history (if there are multiple payment records for the same fee and student)
        $paymentHistory = FeePayment::where('fee_id', $payment->fee_id)
            ->where('student_id', $payment->student_id)
            ->where('id', '!=', $payment->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Fee/PaymentShow', [
            'payment' => $payment,
            'paymentHistory' => $paymentHistory,
        ]);
    }
}
