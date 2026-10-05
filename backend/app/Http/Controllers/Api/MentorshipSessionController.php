<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentorshipRequest;
use App\Models\MentorshipSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MentorshipSessionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mentorship_request_id' => [
                'required',
                'integer',
                'exists:mentorship_requests,id',
            ],
            'scheduled_at' => [
                'required',
                'date',
            ],
            'duration_minutes' => [
                'nullable',
                'integer',
                'min:15',
                'max:480',
            ],
            'meeting_link' => [
                'nullable',
                'string',
                'max:500',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $mentor = $request->user();

        $mentorshipRequest = MentorshipRequest::with([
            'student',
            'mentor',
        ])
            ->where(
                'id',
                $validated['mentorship_request_id']
            )
            ->where(
                'mentor_id',
                $mentor->id
            )
            ->first();

        if (!$mentorshipRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Mentorship request not found.',
            ], 404);
        }

        if ($mentorshipRequest->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'A session can only be created for an accepted mentorship request.',
            ], 422);
        }

        $existingSession = MentorshipSession::where(
            'mentorship_request_id',
            $mentorshipRequest->id
        )->first();

        if ($existingSession) {
            return response()->json([
                'success' => false,
                'message' => 'A session already exists for this mentorship request.',
            ], 409);
        }

        $session = MentorshipSession::create([
            'mentorship_request_id' => $mentorshipRequest->id,
            'student_id' => $mentorshipRequest->student_id,
            'mentor_id' => $mentorshipRequest->mentor_id,
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'] ?? 60,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'scheduled',
        ]);

        $session->load([
            'mentorshipRequest',
            'student',
            'mentor',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mentorship session created successfully.',
            'data' => $session,
        ], 201);
    }

    public function mentorSessions(Request $request): JsonResponse
    {
        $mentor = $request->user();

        $sessions = MentorshipSession::with([
            'student',
            'mentorshipRequest',
        ])
            ->where(
                'mentor_id',
                $mentor->id
            )
            ->latest('scheduled_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }

    public function studentSessions(Request $request): JsonResponse
    {
        $student = $request->user();

        $sessions = MentorshipSession::with([
            'mentor',
            'mentorshipRequest',
        ])
            ->where(
                'student_id',
                $student->id
            )
            ->latest('scheduled_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }
}