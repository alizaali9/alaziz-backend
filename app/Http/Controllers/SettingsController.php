<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function settings()
    {
        if (auth()->user()->role == 1) {
            return view('content.account.settings');
        } else {
            $instructor = Instructor::where('user_id', auth()->user()->id)->first();
            return view('content.account.settings', compact('instructor'));
        }
    }
    public function updateUserName(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->name = $validated['name'];

        if ($user->role != 1) {
            $instructor = Instructor::where('user_id', $user->id)->first();
            if ($instructor) {
                $instructor->name = $validated['name'];
                $instructor->save();
            }
        }

        $user->save();

        return redirect()->back()->with('success', 'Name updated successfully.');
    }

    public function updateUserEmail(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'email' => 'required|email|string|max:255',
        ]);

        $user->email = $validated['email'];

        $user->save();

        return redirect()->back()->with('success', 'Email updated successfully.');
    }
    public function updateInstructorAbout(Request $request, $id)
    {
        $user = Instructor::findOrFail($id);

        $validated = $request->validate([
            'about' => 'required|string|min:10',
        ]);

        $user->about = $validated['about'];

        $user->save();

        return redirect()->back()->with('success', 'About updated successfully.');
    }

    public function updateInstructorSkills(Request $request, $id)
    {
        $user = Instructor::findOrFail($id);

        $validated = $request->validate([
            'skills' => 'required|string',
        ]);

        $user->skills = $validated['skills'];

        $user->save();

        return redirect()->back()->with('success', 'Skills updated successfully.');
    }

    public function updateInstructorPic(Request $request, $id)
    {
        $user = Instructor::findOrFail($id);

        $validated = $request->validate([
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $picturePath = null;
        if ($request->hasFile('picture')) {
            $picturePath =  $validated['picture']->store('instructors', 'public');
        }

        $user->picture = $picturePath;

        $user->save();

        return redirect()->back()->with('success', 'Picture updated successfully.');
    }

    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully.');
    }


}
