<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'fee_type',
        'description',
        'amount',
        'class_id',
        'academic_year',
        'due_date',
        'is_recurring',
        'recurring_period',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Get the class that this fee belongs to.
     */
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the payments for this fee.
     */
    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }

    /**
     * Get the number of payments for this fee.
     */
    public function paymentsCount()
    {
        return $this->payments()->count();
    }
}
