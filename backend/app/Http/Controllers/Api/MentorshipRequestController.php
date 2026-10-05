<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MentorshipRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MentorshipRequestController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Student: Create Mentorship Request
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mentor_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
            'message' => [
                'nullable',
                'string',
            ],
        ]);

        $student = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Make sure selected user is actually a Mentor
        |--------------------------------------------------------------------------
        */

        $mentor = User::where('id', $validated['mentor_id'])
            ->whereHas('role', function ($query) {
                $query->where('role_name', 'Mentor');
            })
            ->first();

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Selected user is not a valid mentor.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent duplicate pending request
        |--------------------------------------------------------------------------
        */

        $existingRequest = MentorshipRequest::where(
            'student_id',
            $student->id
        )
            ->where(
                'mentor_id',
                $mentor->id
            )
            ->where(
                'status',
                'pending'
            )
            ->first();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending request with this mentor.',
            ], 409);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Request
        |--------------------------------------------------------------------------
        */

        $mentorshipRequest = MentorshipRequest::create([
            'student_id' => $student->id,
            'mentor_id' => $mentor->id,
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        $mentorshipRequest->load([
            'student',
            'mentor',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mentorship request sent successfully.',
            'data' => $mentorshipRequest,
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Student: View Own Requests
    |--------------------------------------------------------------------------
    */

    public function studentRequests(
        Request $request
    ): JsonResponse {
        $student = $request->user();

        $requests = MentorshipRequest::with([
            'mentor.role',
            'mentor.mentorProfile',
            'mentor.skills',
        ])
            ->where(
                'student_id',
                $student->id
            )
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Mentor: View Incoming Requests
    |--------------------------------------------------------------------------
    */

    public function mentorRequests(
        Request $request
    ): JsonResponse {
        $mentor = $request->user();

        $requests = MentorshipRequest::with([
            'student.role',
            'student.skills',
        ])
            ->where(
                'mentor_id',
                $mentor->id
            )
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Mentor: Accept Request
    |--------------------------------------------------------------------------
    */

    public function accept(
        Request $request,
        int $id
    ): JsonResponse {
        $mentor = $request->user();

        $mentorshipRequest = MentorshipRequest::where(
            'id',
            $id
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

        if ($mentorshipRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be accepted.',
            ], 422);
        }

        $mentorshipRequest->update([
            'status' => 'accepted',
        ]);

        $mentorshipRequest->load([
            'student',
            'mentor',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mentorship request accepted.',
            'data' => $mentorshipRequest,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Mentor: Reject Request
    |--------------------------------------------------------------------------
    */

    public function reject(
        Request $request,
        int $id
    ): JsonResponse {
        $mentor = $request->user();

        $mentorshipRequest = MentorshipRequest::where(
            'id',
            $id
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

        if ($mentorshipRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending requests can be rejected.',
            ], 422);
        }

        $mentorshipRequest->update([
            'status' => 'rejected',
        ]);

        $mentorshipRequest->load([
            'student',
            'mentor',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mentorship request rejected.',
            'data' => $mentorshipRequest,
        ]);
    }
}