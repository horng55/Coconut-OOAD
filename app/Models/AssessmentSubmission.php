<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssessmentSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'comments',
        'file_path',
        'file_name',
        'file_size',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
