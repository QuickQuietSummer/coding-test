<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $availableRole)
    {
        if (Auth::check() == false) return $next($request);
        $currentRole = $request->user()->role->type;
        if ($currentRole != $availableRole) {
            return Response::json([
                'message' => 'User with role ' . $currentRole . ' cant use action for ' . $availableRole
            ], 401);

        }
        return $next($request);
    }
}
