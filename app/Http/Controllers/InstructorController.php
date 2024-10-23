<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
            'about' => 'required|string|min:10',
            'skills' => 'required|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        $picturePath = null;
        if ($request->hasFile('picture')) {
            $profilePicture = $request->file('picture');

            $extension = $profilePicture->getClientOriginalExtension();
            $fileData = File::get($profilePicture);
            $profilePath = env('GOOGLE_DERIVE_FOLDER_NAME') . '/instructors/instructor_' . uniqid() . '.' . $extension;
            Storage::disk('google')->put($profilePath, $fileData);
            Storage::disk('google')->setVisibility($profilePath, 'public');

            $picturePath = $profilePath;
        }

        if ($user) {
            Instructor::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'about' => $request->about,
                'skills' => $request->skills,
                'picture' => $picturePath,
                'total_students' => 0,
                'courses' => 0
            ]);

            return back()->with('success', 'Instructor has been created successfully.');
        }

        return back()->with('error', 'An error occurred while creating the instructor.');
    }

    public function manage(Request $request)
    {
        $query = Instructor::with('user');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('about', 'LIKE', "%{$search}%")
                    ->orWhere('skills', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        $instructors = $query->get();

        foreach ($instructors as $instructor) {
            $instructor->picture = Storage::disk('google')->url($instructor->picture);
        }

        return view('content.instructors.manage', compact('instructors'));
    }

    public function downloadCSV(Request $request)
    {
        $query = Instructor::query()->with('user');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('about', 'LIKE', "%{$search}%")
                    ->orWhere('skills', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        $instructors = $query->get();

        $csvFileName = 'instructors_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $handle = fopen($csvFileName, 'w');

        $csvData = [
            ['Name', 'Email', 'About', 'Skills']
        ];

        foreach ($instructors as $instructor) {
            $csvData[] = [
                $instructor->name,
                $instructor->user->email,
                $instructor->about,
                $instructor->skills,
            ];
        }

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return response()->download($csvFileName)->deleteFileAfterSend(true);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:instructors,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'about' => 'required|string',
            'skills' => 'required|string',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $instructor = Instructor::find($request->id);

        $instructor->name = $request->name;
        $user = $instructor->user;
        $user->email = $request->email;
        $instructor->about = $request->about;
        $instructor->skills = $request->skills;

        if ($request->hasFile('picture')) {
            if ($instructor->picture) {
                Storage::disk('google')->delete($instructor->picture);
            }
            $profilePicture = $request->file('picture');

            $extension = $profilePicture->getClientOriginalExtension();
            $fileData = File::get($profilePicture);
            $profilePath = env('GOOGLE_DERIVE_FOLDER_NAME') . '/instructors/instructor_' . uniqid() . '.' . $extension;
            Storage::disk('google')->put($profilePath, $fileData);
            Storage::disk('google')->setVisibility($profilePath, 'public');

            $instructor->picture = $profilePath;
        }

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

        $instructors->transform(function ($instructor) {

            $instructor->picture = $instructor->picture ? Storage::disk('google')->url($instructor->picture) : null;

            return $instructor;
        });

        return response()->json($instructors);
    }

    public function getInstructor($id)
    {
        $instructor = Instructor::with('user')->find($id);

        if (!$instructor) {
            return response()->json(['error' => 'Instructor not found'], 404);
        }

        $instructor->picture = Storage::disk('google')->url($instructor->picture);

        return response()->json($instructor);
    }

}
