<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorshipRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'mentor_id',
        'message',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'student_id',
            'id'
        );
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'mentor_id',
            'id'
        );
    }

    public function sessions(): HasMany
{
    return $this->hasMany(
        MentorshipSession::class,
        'mentorship_request_id',
        'id'
    );
}
}