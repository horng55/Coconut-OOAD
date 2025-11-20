<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{

    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'phone_number',
        'type',
        'gender',
        'dob',
        'profile',
        'remember_token',
        'verified',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'profile' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_name', 'name'];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Revoke all tokens when user is deleted (soft delete or hard delete)
        static::deleting(function ($user) {
            $user->tokens()->delete();
        });
    }

    public function recentSearches()
    {
        return $this->hasMany(RecentSearch::class)->latest();
    }

    // School Management Relationships
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function parentUser()
    {
        return $this->hasOne(ParentUser::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'enrollments', 'student_id', 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Send password reset notification
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\MobilePasswordResetNotification($token));
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @return string
     */
    public function verificationUrl()
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            ['id' => $this->getKey(), 'hash' => sha1($this->getEmailForVerification())]
        );
    }

    /**
     * Get the user's full name.
     * Falls back to the 'name' field if first_name and last_name are empty (for backward compatibility).
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        $firstName = trim($this->first_name ?? '');
        $lastName = trim($this->last_name ?? '');
        
        // If both first_name and last_name are empty, try to use the 'name' field as fallback
        if (empty($firstName) && empty($lastName)) {
            // Check if 'name' column exists and has a value (using raw DB query as fallback)
            try {
                $rawUser = \DB::table('users')->where('id', $this->id)->first();
                if ($rawUser && isset($rawUser->name) && !empty(trim($rawUser->name))) {
                    return trim($rawUser->name);
                }
            } catch (\Exception $e) {
                // Column doesn't exist, continue
            }
        }
        
        $fullName = trim($firstName . ' ' . $lastName);
        
        // Return empty string if both are empty (Vue will show N/A)
        return $fullName;
    }

    /**
     * Get the user's name (for backward compatibility).
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->full_name;
    }
}
