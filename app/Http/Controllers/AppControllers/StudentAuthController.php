<?php

namespace App\Http\Controllers\AppControllers;

use App\Http\Controllers\Controller;
use App\Mail\StudentResetPasswordMail;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StudentAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'whatsapp_no' => 'required|string|max:11',
            'password' => 'required|string|min:8|confirmed',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'immi_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors(), "status" => 400], 400);
        }

        $currentYear = date('Y');
        $latestRollNo = Student::whereYear('created_at', $currentYear)
            ->orderByRaw("CAST(SUBSTRING_INDEX(roll_no, '-', -1) AS UNSIGNED) DESC")
            ->value('roll_no');

        $latestSequence = $latestRollNo
            ? intval(substr($latestRollNo, -4)) + 1
            : 1;

        $rollNumber = $currentYear . '-ACP-' . str_pad($latestSequence, 4, '0', STR_PAD_LEFT);

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
            'immi_number' => $request->immi_number,
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


    public function getStudentByRollNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_no' => 'required|string|exists:students,roll_no',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $student = Student::where('roll_no', $request->roll_no)->first();

        $student->picture = $student->picture ? asset('storage/' . $student->picture) : null;

        if (!$student) {
            return response()->json(['status' => 404, 'message' => 'Student not found'], 404);
        }

        return response()->json([
            'status' => 200,
            'student' => $student,
        ], 200);
    }

    public function manage(Request $request)
    {
        $query = Student::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('roll_no', 'LIKE', "%{$search}%")
                    ->orWhere('whatsapp_no', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%")
                    ->orWhere('country', 'LIKE', "%{$search}%")
                    ->orWhere('immi_number', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->get();

        return view('content.students.view-students', compact('students'));
    }

    public function downloadCSV(Request $request)
    {
        $query = Student::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('roll_no', 'LIKE', "%{$search}%")
                    ->orWhere('whatsapp_no', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%")
                    ->orWhere('country', 'LIKE', "%{$search}%")
                    ->orWhere('immi_number', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->get();

        $csvFileName = 'students_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $handle = fopen($csvFileName, 'w');

        $csvData = [
            ['Name', 'Roll Number', 'WhatsApp Number', 'Email', 'City', 'Country', "IMMI Number"]
        ];


        foreach ($students as $student) {
            $csvData[] = [
                $student->name,
                $student->roll_no,
                $student->whatsapp_no,
                $student->email,
                $student->city,
                $student->country,
                $student->immi_number,
            ];
        }

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return response()->download($csvFileName)->deleteFileAfterSend(true);
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
            'api_token' => $token,
            'token_expires_at' => $tokenExpiresAt,
        ]);

        return response()->json([
            'status' => 200,
            'token' => $token,
            'expires_at' => $tokenExpiresAt,
            'roll_no' => $student->roll_no,
            'username' => $student->name,
            'immi_number' => $student->immi_number
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

    public function uploadPicture(Request $request)
    {
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $picturePath = $picture->store('student_pictures', 'public');

            $pictureUrl = Storage::url($picturePath);

            return response()->json([
                'status' => 200,
                'message' => 'Picture uploaded successfully',
                'picture_url' => $picturePath,
            ], 200);
        }

        return response()->json([
            'status' => 400,
            'message' => 'No picture found',
        ], 400);
    }

    public function editStudent(Request $request, $rollNumber)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:students,email,' . $rollNumber . ',roll_no',
            'whatsapp_no' => 'sometimes|string|max:11',
            'password' => 'sometimes|string|min:8|confirmed',
            'city' => 'sometimes|string|max:255',
            'country' => 'sometimes|string|max:255',
            'immi_number' => 'sometimes|string|max:255',
            'picture' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'errors' => $validator->errors()], 400);
        }

        $student = Student::where('roll_no', $rollNumber)->first();

        if (!$student) {
            return response()->json(['status' => 404, 'message' => 'Student not found'], 404);
        }

        $student->update([
            'name' => $request->name ?? $student->name,
            'email' => $request->email ?? $student->email,
            'whatsapp_no' => $request->whatsapp_no ?? $student->whatsapp_no,
            'password' => $request->password ? Hash::make($request->password) : $student->password,
            'city' => $request->city ?? $student->city,
            'country' => $request->country ?? $student->country,
            'immi_number' => $request->immi_number ?? $student->immi_number,
            'picture' => $request->picture ?? $student->picture,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Student details updated successfully',
            'student' => $student,
        ], 200);
    }

    public function deleteStudent($id)
    {

        $student = Student::find($id);

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        $student->delete();

        return redirect()->back()->with('success', 'Student and related data deleted successfully.');
    }
    public function deleteStudentAPI($roll_no)
    {
        $student = Student::where('roll_no', $roll_no)->first();

        if (!$student) {
            return response()->json([
                'status' => 404,
                'message' => 'Student not found',
            ], 404);
        }

        $student->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Student deleted successfully',
        ], 200);
    }

    public function deleteStudentPage()
    {

        return view('content.auth.delete-student');
    }
    public function deleteStudentAccount(Request $request)
    {
        $student = Student::where('roll_no', $request->input('roll_no'))->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        $student->delete();

        return redirect()->back()->with('success', 'Student and related data deleted successfully.');
    }



}
