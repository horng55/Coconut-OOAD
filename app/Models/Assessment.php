<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_id',
        'assessment_type',
        'assessment_name',
        'score',
        'max_score',
        'assessment_date',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'assessment_date' => 'date',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'assessment_id');
    }

    public function submissions()
    {
        return $this->hasMany(AssessmentSubmission::class);
    }
}
