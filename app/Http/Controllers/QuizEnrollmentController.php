<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizEnrollment;
use App\Models\Student;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizEnrollmentController extends Controller
{

    public function create()
    {
        $students = Student::all();
        $quizzes = Quiz::all();

        return view('content.quiz-enrollment.create', compact("students", "quizzes"));
    }

    public function manage()
    {
        $enrollments = QuizEnrollment::with(['quiz', 'student'])->get();
        return view('content.quiz-enrollment.manage', compact('enrollments'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student' => 'required|exists:students,id',
            'quiz' => 'required|exists:quizzes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $existingEnrollment = QuizEnrollment::where('student_id', $request->student)
            ->where('quiz_id', $request->quiz)
            ->first();

        $quiz = Quiz::findOrFail($request->quiz);

        if ($existingEnrollment) {
            return redirect()->back()->with('error', 'Student is already enrolled in this quiz.');
        }

        $enrollment = QuizEnrollment::create([
            'student_id' => $request->student,
            'quiz_id' => $request->quiz,
            'remaining_tries' => $quiz->tries
        ]);

        if ($enrollment) {
            return redirect()->back()->with('success', 'Student enrolled in quiz successfully.');
        } else {
            return redirect()->back()->with('error', 'Something went wrong.');
        }
    }

    public function checkEnrollment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_no' => 'required|exists:students,roll_no',
            'quiz_id' => 'required|exists:quizzes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $student = Student::where('roll_no', $request->roll_no)->first();

        if (!$student) {
            return response()->json(['status' => 404, 'message' => 'Student not found'], 404);
        }

        $existingEnrollment = QuizEnrollment::where('student_id', $student->id)
            ->where('quiz_id', $request->quiz_id)
            ->first();

        if ($existingEnrollment) {
            return response()->json(['status' => 200, 'enrolled' => true, 'remaining_tries' => $existingEnrollment->remaining_tries], 200);
        }

        return response()->json(['status' => 200, 'enrolled' => false], 200);
    }

    public function index()
    {
        $enrollments = QuizEnrollment::with(['student', 'quiz'])->get();
        return response()->json($enrollments);
    }

    public function getQuizzesOfStudent($roll_no)
    {
        $validator = Validator::make(['roll_no' => $roll_no], [
            'roll_no' => 'required|string|exists:students,roll_no',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $student = Student::where('roll_no', $roll_no)->first();


        if (!$student) {
            return response()->json(['status' => 404, 'message' => 'Student not found'], 404);
        }

        $quizzes = QuizEnrollment::where('student_id', $student->id)
            ->with('quiz')
            ->get()
            ->pluck('quiz');

        $quizzes->transform(function ($quiz) {

            $quiz->thumbnail = $quiz->thumbnail ? asset('storage/' . $quiz->thumbnail) : null;
            $subcategory = Subcategory::where('id', $quiz->sub_category)->first();

            // dd($subcategory);

            $quiz->sub_category = $subcategory->name;
            return $quiz;
        });


        return response()->json([
            'status' => 200,
            'student' => $student->name,
            'roll_no' => $student->roll_no,
            'quizzes' => $quizzes,
        ], 200);
    }

    public function updateMarksPercentage(Request $request, $roll_no, $quizId)
    {
        $validator = Validator::make($request->all(), [
            'marks_percentage' => 'required|numeric|min:0|max:100',
            'remaining_tries' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $student = Student::where('roll_no', $roll_no)->first();
        $quiz = Quiz::find($quizId);

        if (!$quiz) {
            return response()->json(['status' => 404, 'message' => 'Quiz not found'], 404);
        }

        if($request->remaining_tries >= $quiz->tries){
            return response()->json(['status' => 422, 'error' => "You can't exceed the maximum allowed tries"], 422);
        }

        $quizEnrollment = QuizEnrollment::where('student_id', $student->id)
            ->where('quiz_id', $quizId)
            ->first();

        if (!$quizEnrollment) {
            return response()->json(['status' => 404, 'message' => 'Quiz enrollment not found'], 404);
        }

        $quizEnrollment->update([
            'marks_percentage' => $request->marks_percentage,
            'remaining_tries' => $request->remaining_tries
        ]);

        return response()->json(['status' => 200, 'message' => 'Marks percentage updated successfully'], 200);
    }

    public function delete($id)
    {
        $quizEnrollment = QuizEnrollment::find($id);

        if (!$quizEnrollment) {
            return redirect()->back()->with('error', 'Quiz enrollment not found.');
        }

        $deleted = $quizEnrollment->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Quiz enrollment deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete quiz enrollment.');
        }
    }
}
