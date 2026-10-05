<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $roleName = $user->role?->role_name;

        if (
            !$roleName ||
            !in_array($roleName, $roles)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have permission to perform this action.',
            ], 403);
        }

        return $next($request);
    }
}