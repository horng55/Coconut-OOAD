<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{
    protected $fillable = [
        'user_username',
        'actionable_id',
        'actionable_type',
        'action_type',
        'portal',
        'ip',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function actionable()
    {
        return $this->morphTo();
    }
}
