<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'fee_id',
        'student_id',
        'amount',
        'amount_paid',
        'remaining_amount',
        'due_date',
        'payment_date',
        'payment_method',
        'transaction_id',
        'receipt_number',
        'status',
        'paid_by',
        'notes',
        'academic_year',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    /**
     * Get the fee that this payment belongs to.
     */
    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    /**
     * Get the student that this payment belongs to.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who recorded this payment.
     */
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
