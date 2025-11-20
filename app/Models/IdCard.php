<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IdCard extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'card_type',
        'student_id',
        'teacher_id',
        'card_number',
        'issue_date',
        'expiry_date',
        'status',
        'photo_path',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function getOwnerAttribute()
    {
        if ($this->card_type === 'student' && $this->student) {
            return $this->student->user;
        } elseif ($this->card_type === 'teacher' && $this->teacher) {
            return $this->teacher->user;
        }
        return null;
    }
}
