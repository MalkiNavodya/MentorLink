<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MentorProfile extends Model
{
    protected $primaryKey = 'mentor_profile_id';

    protected $fillable = [
        'user_id',
        'bio',
        'university',
        'degree',
        'academic_year',
        'availability',
    ];

    protected $casts = [
        'academic_year' => 'integer',
    ];

    /**
     * Mentor profile belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}