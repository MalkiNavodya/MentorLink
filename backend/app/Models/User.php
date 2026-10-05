<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Fields
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'role_id',
            'role_id'
        );
    }

    public function mentorProfile(): HasOne
    {
        return $this->hasOne(
            MentorProfile::class,
            'user_id',
            'id'
        );
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(
            Skill::class,
            'user_skills',
            'user_id',
            'skill_id'
        )->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Mentorship Request Relationships
    |--------------------------------------------------------------------------
    */

    public function mentorshipRequestsAsStudent(): HasMany
    {
        return $this->hasMany(
            MentorshipRequest::class,
            'student_id',
            'id'
        );
    }

    public function mentorshipRequestsAsMentor(): HasMany
    {
        return $this->hasMany(
            MentorshipRequest::class,
            'mentor_id',
            'id'
        );
    }
}