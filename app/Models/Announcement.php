<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'content',
        'target_audience',
        'class_id',
        'created_by',
        'publish_date',
        'expiry_date',
        'is_pinned',
        'priority',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'expiry_date' => 'date',
        'is_pinned' => 'boolean',
        'target_audience' => 'array',
    ];

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
