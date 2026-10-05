<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MentorController extends Controller
{
    /**
     * Get all mentors.
     */
    public function index(): JsonResponse
    {
        $mentors = User::with([
            'role',
            'mentorProfile',
            'skills',
        ])
            ->whereHas('role', function ($query) {
                $query->where('role_name', 'Mentor');
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $mentors,
        ]);
    }

    /**
     * Get one mentor.
     */
    public function show(int $id): JsonResponse
    {
        $mentor = User::with([
            'role',
            'mentorProfile',
            'skills',
        ])
            ->whereHas('role', function ($query) {
                $query->where('role_name', 'Mentor');
            })
            ->find($id);

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mentor,
        ]);
    }

    /**
     * Create a mentor.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
            ],

            'bio' => [
                'nullable',
                'string',
            ],

            'university' => [
                'nullable',
                'string',
                'max:150',
            ],

            'degree' => [
                'nullable',
                'string',
                'max:150',
            ],

            'academic_year' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],

            'availability' => [
                'nullable',
                'string',
                'max:100',
            ],

            'skill_ids' => [
                'nullable',
                'array',
            ],

            'skill_ids.*' => [
                'integer',
                'exists:skills,skill_id',
            ],
        ]);

        $role = Role::where(
            'role_name',
            'Mentor'
        )->first();

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor role does not exist.',
            ], 500);
        }

        $mentor = DB::transaction(function () use (
            $validated,
            $role
        ) {
            $mentor = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role_id' => $role->role_id,
            ]);

            $mentor->mentorProfile()->create([
                'bio' => $validated['bio'] ?? null,
                'university' =>
                    $validated['university'] ?? null,
                'degree' =>
                    $validated['degree'] ?? null,
                'academic_year' =>
                    $validated['academic_year'] ?? null,
                'availability' =>
                    $validated['availability'] ?? null,
            ]);

            if (!empty($validated['skill_ids'])) {
                $mentor->skills()->sync(
                    $validated['skill_ids']
                );
            }

            return $mentor;
        });

        $mentor->load([
            'role',
            'mentorProfile',
            'skills',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mentor created successfully.',
            'data' => $mentor,
        ], 201);
    }

    /**
     * Update a mentor.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse {
        $mentor = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Mentor');
        })->find($id);

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor not found.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                'max:255',
                Rule::unique(
                    'users',
                    'email'
                )->ignore($mentor->id),
            ],

            'password' => [
                'sometimes',
                'nullable',
                'string',
                'min:8',
            ],

            'bio' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'university' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],

            'degree' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],

            'academic_year' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],

            'availability' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'skill_ids' => [
                'sometimes',
                'array',
            ],

            'skill_ids.*' => [
                'integer',
                'exists:skills,skill_id',
            ],
        ]);

        DB::transaction(function () use (
            $mentor,
            $validated
        ) {
            if (array_key_exists(
                'name',
                $validated
            )) {
                $mentor->name = $validated['name'];
            }

            if (array_key_exists(
                'email',
                $validated
            )) {
                $mentor->email = $validated['email'];
            }

            if (
                array_key_exists(
                    'password',
                    $validated
                ) &&
                !empty($validated['password'])
            ) {
                $mentor->password =
                    $validated['password'];
            }

            $mentor->save();

            $profile = $mentor->mentorProfile;

            if (!$profile) {
                $profile =
                    $mentor->mentorProfile()
                        ->create([]);
            }

            $profileFields = [
                'bio',
                'university',
                'degree',
                'academic_year',
                'availability',
            ];

            foreach ($profileFields as $field) {
                if (
                    array_key_exists(
                        $field,
                        $validated
                    )
                ) {
                    $profile->$field =
                        $validated[$field];
                }
            }

            $profile->save();

            if (
                array_key_exists(
                    'skill_ids',
                    $validated
                )
            ) {
                $mentor->skills()->sync(
                    $validated['skill_ids']
                );
            }
        });

        $mentor->load([
            'role',
            'mentorProfile',
            'skills',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mentor updated successfully.',
            'data' => $mentor,
        ]);
    }

    /**
     * Delete a mentor.
     */
    public function destroy(int $id): JsonResponse
    {
        $mentor = User::whereHas('role', function ($query) {
            $query->where('role_name', 'Mentor');
        })->find($id);

        if (!$mentor) {
            return response()->json([
                'success' => false,
                'message' => 'Mentor not found.',
            ], 404);
        }

        DB::transaction(function () use ($mentor) {
            $mentor->skills()->detach();

            if ($mentor->mentorProfile) {
                $mentor->mentorProfile()->delete();
            }

            $mentor->tokens()->delete();

            $mentor->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Mentor deleted successfully.',
        ]);
    }
}