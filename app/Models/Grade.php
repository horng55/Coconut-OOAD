<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'class_id',
        'assessment_id',
        'assessment_type',
        'assessment_name',
        'score',
        'max_score',
        'percentage',
        'letter_grade',
        'assessment_date',
        'comments',
        'graded_by',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'assessment_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}
