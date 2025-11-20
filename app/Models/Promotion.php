<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'from_class_id',
        'to_class_id',
        'from_academic_year',
        'to_academic_year',
        'promotion_date',
        'promotion_type',
        'status',
        'promoted_by',
        'notes',
        'criteria_met',
    ];

    protected $casts = [
        'promotion_date' => 'date',
        'criteria_met' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fromClass()
    {
        return $this->belongsTo(ClassModel::class, 'from_class_id');
    }

    public function toClass()
    {
        return $this->belongsTo(ClassModel::class, 'to_class_id');
    }

    public function promotedBy()
    {
        return $this->belongsTo(User::class, 'promoted_by');
    }

    public function getPromotionTypeLabelAttribute()
    {
        $types = [
            'automatic' => 'Automatic',
            'manual' => 'Manual',
            'conditional' => 'Conditional',
            'repeated' => 'Repeated',
        ];

        return $types[$this->promotion_type] ?? $this->promotion_type;
    }

    public function getStatusLabelAttribute()
    {
        $statuses = [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}

