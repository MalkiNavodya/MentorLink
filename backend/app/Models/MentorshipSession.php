<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MentorshipSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentorship_request_id',
        'student_id',
        'mentor_id',
        'scheduled_at',
        'duration_minutes',
        'meeting_link',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    public function mentorshipRequest(): BelongsTo
    {
        return $this->belongsTo(
            MentorshipRequest::class,
            'mentorship_request_id',
            'id'
        );
    }

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
}