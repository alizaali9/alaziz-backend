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
        $query = QuizEnrollment::with(['quiz', 'student']);

        if (request()->has('search')) {
            $search = request()->get('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('roll_no', 'LIKE', "%{$search}%");
            })->orWhereHas('quiz', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        if (request()->has('download')) {
            return $this->downloadQuizEnrollmentsCSV($query->get());
        }

        $enrollments = $query->get();

        return view('content.quiz-enrollment.manage', compact('enrollments'));
    }
    public function downloadQuizEnrollmentsCSV($enrollments)
    {
        $csvData = [
            ['Student Name', 'Student Roll No', 'Quiz Name']
        ];

        foreach ($enrollments as $enrollment) {
            $csvData[] = [
                $enrollment->student->name,
                $enrollment->student->roll_no,
                $enrollment->quiz->name,
            ];
        }

        $filename = 'quiz-enrollments_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $handle = fopen($filename, 'w');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student' => 'required|exists:students,id',
            'quizzes' => 'required|array',
            'quizzes.*' => 'required|exists:quizzes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $studentId = $request->student;
        $quizIds = $request->quizzes;

        $enrolledQuizzes = [];
        $skippedQuizzes = [];

        foreach ($quizIds as $quizId) {
            $existingEnrollment = QuizEnrollment::where('student_id', $studentId)
                ->where('quiz_id', $quizId)
                ->first();

            if ($existingEnrollment) {
                $skippedQuizzes[] = $quizId;
                continue;
            }

            $quiz = Quiz::find($quizId);

            if (!$quiz) {
                $skippedQuizzes[] = $quizId;
                continue;
            }

            $enrollment = QuizEnrollment::create([
                'student_id' => $studentId,
                'quiz_id' => $quizId,
                'remaining_tries' => $quiz->tries,
            ]);

            if ($enrollment) {
                $enrolledQuizzes[] = $quiz->name;
            } else {
                $skippedQuizzes[] = $quiz->name;
            }
        }

        if (count($enrolledQuizzes) > 0) {
            $successMessage = 'Student successfully enrolled in quizzes: '
                . implode(', ', $enrolledQuizzes) . '.';
        } else {
            $successMessage = '';
        }

        if (count($skippedQuizzes) > 0) {
            $errorMessage = 'Some quizzes were skipped: '
                . implode(', ', $skippedQuizzes) . '.';
        } else {
            $errorMessage = '';
        }

        return redirect()->back()->with([
            'success' => $successMessage,
            'error' => $errorMessage,
        ]);
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

        $existingEnrollment = QuizEnrollment::where(function ($query) use ($student, $request) {
            $query->where('student_id', $student->id)
                ->where('quiz_id', $request->quiz_id);
        })->first();

        // dd($existingEnrollment);

        $totalEnrolled = QuizEnrollment::where('quiz_id', $request->quiz_id)->count();

        if ($existingEnrollment) {
            if ($existingEnrollment->is_active == 1) {
                return response()->json([
                    'status' => 200,
                    'enrolled' => true,
                    'marks_percentage' => $existingEnrollment->marks_percentage == null ? 0 : $existingEnrollment->marks_percentage,
                    'remaining_tries' => $existingEnrollment->remaining_tries,
                    'total_enrolled' => $totalEnrolled
                ], 200);
            }
        }

        return response()->json([
            'status' => 200,
            'enrolled' => false,
            'total_enrolled' => $totalEnrolled
        ], 200);
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
            ->where('is_active', true)
            ->with('quiz')
            ->get()
            ->pluck('quiz');

        $quizzes->transform(function ($quiz) {
            if ($quiz->status) {
                $quiz->thumbnail = $quiz->thumbnail ? asset('storage/' . $quiz->thumbnail) : null;

                $subcategory = Subcategory::where('id', $quiz->sub_category)->first();
                $quiz->sub_category = $subcategory->name;

                $quiz->questions_count = $quiz->questions()->count();

                return $quiz;
            }else{
                return null;
            }

        });

        $quizzes = $quizzes->filter();


        return response()->json([
            'status' => 200,
            'student' => $student->name,
            'roll_no' => $student->roll_no,
            'quizzes' => $quizzes->values(),
        ], 200);
    }

    public function toggleStatus(Request $request, $id)
    {
        $enrollment = QuizEnrollment::find($id);

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Enrollment not found.']);
        }

        $enrollment->is_active = $request->status;
        $enrollment->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
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

        if ($request->remaining_tries >= $quiz->tries) {
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
