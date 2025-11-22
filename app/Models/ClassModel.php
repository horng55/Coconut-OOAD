<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'code',
        'description',
        'academic_year',
        'status',
        'max_students',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject')->withTimestamps();
    }

    // Keep subject() for backward compatibility (returns first subject)
    public function getSubjectAttribute()
    {
        return $this->subjects()->first();
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'class_teacher')->withTimestamps();
    }

    // Keep teacher() for backward compatibility (returns first teacher)
    // Note: This is a method, not a relationship, so it returns the model instance
    public function getTeacherAttribute()
    {
        return $this->teachers()->first();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments', 'class_id', 'student_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'class_id');
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'class_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'class_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'class_id');
    }

    public function promotionsFrom()
    {
        return $this->hasMany(Promotion::class, 'from_class_id');
    }

    public function promotionsTo()
    {
        return $this->hasMany(Promotion::class, 'to_class_id');
    }
}
