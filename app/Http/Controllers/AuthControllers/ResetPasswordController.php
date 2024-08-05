<?php

namespace App\Http\Controllers\AuthControllers;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    //
    public function index()
    {
        return view('content.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'No user found with this email address.');
        }

        try {
            $token = Password::createToken($user);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email));
            return back()->with('success', 'Reset password link sent successfully.');

        } catch (\Exception $e) {
            // dd($e);
            return back()->with('error', 'Failed to send reset password link. Please try again later.');
        }
    }

    public function showResetForm(Request $request)
    {

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'role' => 'nullable|in:student,admin',
        ]);

        $tokenExists = DB::table('password_reset_tokens')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->exists();

        if (!$tokenExists) {
            return view('content.auth.reset-link-expired');
        }

        return view('content.auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email,
            'role' => $request->role,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'role' => 'required|in:student,admin',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $email = $request->input('email');
        $token = $request->input('token');
        $newPassword = $request->input('password');

        $resetToken = DB::table('password_reset_tokens')->where('email', $email)->first();


        if (!$resetToken || $resetToken->token !== $token) {
            return view('content.auth.reset-link-expired');
        }

        // dd($resetToken, $token, $resetToken->token !== $token, $request->role == "student");
        if($request->role == "student") {
            $user = Student::where('email', $email)->first();
        } else {
            $user = User::where('email', $email)->first();
        }


        if ($user) {
            $user->password = Hash::make($newPassword);
            // dd($user, $newPassword);
            $user->save();
            $response = view('content.auth.reset-successful');

            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return $response;
        }

        return view('content.auth.reset-link-expired');
    }

}
