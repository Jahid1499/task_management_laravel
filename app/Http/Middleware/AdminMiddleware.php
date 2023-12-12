<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_id=$request->header('id');
        $user = User::findOrFail($user_id);
        if ($user->role !== "admin")
        {
            return response()->json(['error' => 'unauthorised request'], 401);
        }
        return $next($request);
    }
}
