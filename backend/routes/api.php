<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MentorController;
use App\Http\Controllers\Api\MentorshipRequestController;
use App\Http\Controllers\Api\MentorshipSessionController;

Route::post('/register', [
    AuthController::class,
    'register',
]);

Route::post('/login', [
    AuthController::class,
    'login',
]);

/*
|--------------------------------------------------------------------------
| Public Mentor Routes
|--------------------------------------------------------------------------
*/

Route::get('/mentors', [
    MentorController::class,
    'index',
]);

Route::get('/mentors/{id}', [
    MentorController::class,
    'show',
]);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | General Authenticated Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/me', [
        AuthController::class,
        'me',
    ]);

    Route::post('/logout', [
        AuthController::class,
        'logout',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Mentor Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Mentor')->group(function () {

        // Mentor CRUD
        Route::post('/mentors', [
            MentorController::class,
            'store',
        ]);

        Route::put('/mentors/{id}', [
            MentorController::class,
            'update',
        ]);

        Route::delete('/mentors/{id}', [
            MentorController::class,
            'destroy',
        ]);

        // View mentorship requests received by mentor
        Route::get('/mentor/mentorship-requests', [
            MentorshipRequestController::class,
            'mentorRequests',
        ]);

        // Accept mentorship request
        Route::put('/mentorship-requests/{id}/accept', [
            MentorshipRequestController::class,
            'accept',
        ]);

        // Reject mentorship request
        Route::put('/mentorship-requests/{id}/reject', [
            MentorshipRequestController::class,
            'reject',
        ]);

        // Create mentorship session
        Route::post('/mentorship-sessions', [
            MentorshipSessionController::class,
            'store',
        ]);

        // View mentor's sessions
        Route::get('/mentor/mentorship-sessions', [
            MentorshipSessionController::class,
            'mentorSessions',
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Student Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:Student')->group(function () {

        // Create mentorship request
        Route::post('/mentorship-requests', [
            MentorshipRequestController::class,
            'store',
        ]);

        // View student's mentorship requests
        Route::get('/mentorship-requests', [
            MentorshipRequestController::class,
            'studentRequests',
        ]);

        // View student's mentorship sessions
        Route::get('/student/mentorship-sessions', [
            MentorshipSessionController::class,
            'studentSessions',
        ]);
    });
});