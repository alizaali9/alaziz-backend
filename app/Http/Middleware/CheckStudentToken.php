<?php

namespace App\Http\Middleware;

use App\Models\Student;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        // dd($token);


        if (!$token) {
            return response()->json(['error' => 'Token not provided', 'status' => "401"], 401);
        }

        $student = Student::where('api_token', $token)
            ->first();

        // dd($student);


        if (!$student) {
            return response()->json(['error' => 'Invalid or expired token', 'status' => "401"], 401);
        }

        if ($student->token_expires_at < now()) {
            return response()->json(['error' => 'Token has expired', 'status' => "401"], 401);
        }

        $request->attributes->set('student', $student);

        return $next($request);
    }
}
