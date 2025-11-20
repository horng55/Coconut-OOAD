<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_type',
        'title',
        'description',
        'parameters',
        'generated_by',
        'file_path',
        'status',
        'generated_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'generated_at' => 'datetime',
    ];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getReportTypeLabelAttribute()
    {
        $types = [
            'student_performance' => 'Student Performance',
            'class_performance' => 'Class Performance',
            'attendance' => 'Attendance',
            'grade_distribution' => 'Grade Distribution',
            'teacher_workload' => 'Teacher Workload',
            'enrollment' => 'Enrollment',
            'custom' => 'Custom Report',
        ];

        return $types[$this->report_type] ?? $this->report_type;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Pending',
            'generating' => 'Generating',
            'completed' => 'Completed',
            'failed' => 'Failed',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}

