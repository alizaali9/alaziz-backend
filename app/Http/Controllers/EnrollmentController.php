<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends Controller
{

    public function create(){
        $students = Student::all();
        $courses = Course::all();

        return view('content.enrollments.create', compact("students", "courses"));
    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'student' => 'required|exists:students,id',
            'course' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }


        $existingEnrollment = Enrollment::where('student_id', $request->student)
                                        ->where('course_id', $request->course)
                                        ->first();

        if ($existingEnrollment) {
            return redirect()->back()->with('error', 'Student is already in this course.');
        }

        $enrollment = Enrollment::create([
            'student_id' => $request->student,
            'course_id' => $request->course,
        ]);

        if($enrollment){
            return redirect()->back()->with('success', 'Student enrolled successfully.');
        }else {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function checkEnrollment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }


        $existingEnrollment = Enrollment::where('student_id', $request->student_id)
                                        ->where('course_id', $request->course_id)
                                        ->first();

        if ($existingEnrollment) {
            return response()->json(['status' => 200, 'enrolled' => true], 200);
        }

        return response()->json(['status' => 200, 'enrolled' => false], 200);
    }

    public function index()
    {
        $enrollments = Enrollment::with(['student', 'course'])->get();
        return response()->json($enrollments);
    }
}
