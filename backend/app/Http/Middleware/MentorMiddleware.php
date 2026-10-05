<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MentorMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if (
            !$user->role ||
            $user->role->role_name !== 'Mentor'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. Mentor access required.',
            ], 403);
        }

        return $next($request);
    }
}