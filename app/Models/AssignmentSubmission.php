<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignmentSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'comments',
        'file_path',
        'file_name',
        'file_size',
        'score',
        'teacher_feedback',
        'status',
        'submitted_at',
        'graded_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function isLate()
    {
        $dueDateTime = $this->assignment->due_date->format('Y-m-d') . ' ' . ($this->assignment->due_time ?? '23:59:59');
        return $this->submitted_at > $dueDateTime;
    }
}
