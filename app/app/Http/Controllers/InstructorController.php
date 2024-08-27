<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class InstructorController extends Controller
{
    public function index()
    {
        return view('content.instructors.create');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',  // corrected to 'users'
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'about' => 'required|string|min:10',
            'skills' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 2,
            'remember_token' => Str::random(10),
        ]);

        if ($user) {
            Instructor::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'about' => $request->about,
                'skills' => $request->skills,
                'total_students' => 0,
                'courses' => 0,
                'reviews' => 0,
            ]);

            return back()->with('success', 'Instructor has been created successfully.');
        }

        return back()->with('error', 'An error occurred while creating the instructor.');
    }

    public function show()
    {
        $instructors = Instructor::with('user')->get();

        return view('content.instructors.manage', compact('instructors'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:instructors,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'about' => 'required|string',
            'skills' => 'required|string',
        ]);

        $instructor = Instructor::find($request->id);

        $instructor->name = $request->name;
        $user = $instructor->user;
        $user->email = $request->email;
        $instructor->about = $request->about;
        $instructor->skills = $request->skills;

        $user->save();
        $instructor->save();

        return redirect()->back()->with('success', 'Instructor updated successfully');
    }

    public function destroy($id)
    {
        $instructor = Instructor::findOrFail($id);

        $user = User::findOrFail($instructor->user_id);

        $instructor->delete();

        $user->delete();

        return back()->with('success', 'Instructor and associated user have been deleted successfully.');
    }

    public function getAllInstructors()
    {
        $instructors = Instructor::with('user')->get();
        return response()->json($instructors);
    }

    public function getInstructor($id)
    {
        $instructor = Instructor::with('user')->find($id);

        if (!$instructor) {
            return response()->json(['error' => 'Instructor not found'], 404);
        }

        return response()->json($instructor);
    }

}
