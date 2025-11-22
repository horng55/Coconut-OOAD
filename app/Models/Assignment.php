<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'teacher_id',
        'title',
        'description',
        'instructions',
        'due_date',
        'due_time',
        'max_score',
        'attachment_path',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function isOverdue()
    {
        $dueDateTime = $this->due_date->format('Y-m-d') . ' ' . ($this->due_time ?? '23:59:59');
        return now() > $dueDateTime;
    }
}
