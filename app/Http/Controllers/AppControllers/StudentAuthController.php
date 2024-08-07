<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Mail\StudentResetPasswordMail;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StudentAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'whatsapp_no' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $currentYear = date('Y');

        $studentCount = Student::whereYear('created_at', $currentYear)->count() + 1;

        $rollNumber = $currentYear . 'AAI' . $studentCount;

        $token = 'Bearer ' . Str::random(60);
        $tokenExpiresAt = now()->addDays(30);

        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp_no' => $request->whatsapp_no,
            'password' => Hash::make($request->password),
            'city' => $request->city,
            'country' => $request->country,
            'roll_no' => $rollNumber,
            'api_token' => $token,
            'token_expires_at' => $tokenExpiresAt,
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Account created successfully',
            'roll_no' => $rollNumber,
            'token' => $token,
        ], 201);
    }




    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $student = Student::where('email', $credentials['email'])->first();

        if (!$student) {
            return response()->json(['status' => 401, 'message' => 'Unauthorized'], 401);
        }

        if (!Hash::check($credentials['password'], $student->password)) {
            return response()->json(['status' => 401, 'message' => 'Your password is wrong'], 401);
        }

        $token = 'Bearer ' . Str::random(60);
        $tokenExpiresAt = now()->addDays(30);

        $student->update([
            'api_token' => `Bearer $token`,
            'token_expires_at' => $tokenExpiresAt,
        ]);

        return response()->json([
            'status' => 200,
            'token' => $token,
            'expires_at' => $tokenExpiresAt,
        ], 200);
    }



    public function forgotPassword(Request $request)
    {
        $email = $request->input('email');
        $student = Student::where('email', $email)->first();

        if (!$student) {
            return response()->json(['status' => 404, 'message' => 'User not found'], 404);
        }
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        try {
            Mail::to($email)->send(new StudentResetPasswordMail($token, $email));

            return response()->json(['status' => 200, 'message' => 'Reset token sent to your email'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 400, 'message' => 'Failed to send reset password link. Please try again later.'], 400);
        }
    }
}
