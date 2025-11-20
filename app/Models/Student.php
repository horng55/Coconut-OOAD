<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'parent_id',
        'student_id',
        'admission_date',
        'status',
        'medical_info',
    ];

    protected $casts = [
        'admission_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ParentUser::class, 'parent_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'enrollments', 'student_id', 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function idCards()
    {
        return $this->hasMany(IdCard::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    /**
     * Get the fee payments for this student.
     */
    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }
}
