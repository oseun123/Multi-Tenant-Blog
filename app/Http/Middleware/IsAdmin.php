<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the authenticated user is from the admin guard
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        return ResponseHelper::withError('Access denied', [], 403);
    }
}
