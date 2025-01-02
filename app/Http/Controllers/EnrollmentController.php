<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends Controller
{

    public function create()
    {
        $students = Student::all();
        $courses = Course::all();

        return view('content.enrollments.create', compact("students", "courses"));
    }
    public function manage()
    {
        $query = Enrollment::with(['course', 'student']);

        if (request()->has('search')) {
            $search = request()->get('search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('roll_no', 'LIKE', "%{$search}%");
            })->orWhereHas('course', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        if (request()->has('download')) {
            return $this->downloadEnrollmentsCSV($query->get());
        }

        $enrollments = $query->get();

        return view('content.enrollments.manage', compact('enrollments'));
    }

    protected function downloadEnrollmentsCSV($enrollments)
    {
        $csvData = [
            ['Student Name', 'Student Roll No', 'Course Name', 'Status']
        ];

        foreach ($enrollments as $enrollment) {
            $csvData[] = [
                $enrollment->student->name,
                $enrollment->student->roll_no,
                $enrollment->course->name,
                $enrollment->is_active,
            ];
        }

        $filename = 'enrollments_' . now()->format('Y_m_d_H_i_s') . '.csv';
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
            'courses' => 'required|array',
            'courses.*' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $studentId = $request->student;
        $courseIds = $request->courses;

        $enrolledCourses = [];
        $skippedCourses = [];

        foreach ($courseIds as $courseId) {
            $existingEnrollment = Enrollment::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->first();

            if ($existingEnrollment) {
                $skippedCourses[] = $courseId;
                continue;
            }

            $course = Course::find($courseId);

            if (!$course) {
                $skippedCourses[] = $courseId;
                continue;
            }

            $enrollment = Enrollment::create([
                'student_id' => $studentId,
                'course_id' => $courseId,
            ]);

            if ($enrollment) {
                $enrolledCourses[] = $course->name;
            } else {
                $skippedCourses[] = $course->name;
            }
        }

        if (count($enrolledCourses) > 0) {
            $successMessage = 'Student successfully enrolled in courses: '
                . implode(', ', $enrolledCourses) . '.';
        } else {
            $successMessage = '';
        }

        if (count($skippedCourses) > 0) {
            $errorMessage = 'Some courses were skipped: '
                . implode(', ', $skippedCourses) . '.';
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
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 422, 'errors' => $validator->errors()], 422);
        }

        $student = Student::where('roll_no', $request->roll_no)->first();

        if (!$student) {
            return response()->json(['status' => 404, 'message' => 'Student not found'], 404);
        }

        $existingEnrollment = Enrollment::where(function ($query) use ($student, $request) {
            $query->where('student_id', $student->id)
                ->where('course_id', $request->course_id);
        })->first();


        if ($existingEnrollment) {
            if ($existingEnrollment->is_active == 1) {
                return response()->json(['status' => 200, 'enrolled' => true], 200);
            }
        }

        return response()->json(['status' => 200, 'enrolled' => false], 200);
    }


    public function toggleStatus(Request $request, $id)
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json(['success' => false, 'message' => 'Enrollment not found.']);
        }

        $enrollment->is_active = $request->status;
        $enrollment->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    public function index()
    {
        $enrollments = Enrollment::with(['student', 'course'])->get();
        return response()->json($enrollments);
    }

    public function getCoursesOfStudent($roll_no)
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

        $courses = Enrollment::where('student_id', $student->id)
            ->where('is_active', true)
            ->with('course')
            ->get()
            ->pluck('course');

        $courses->transform(function ($course) {
            $course->thumbnail = $course->thumbnail ? asset('storage/' . $course->thumbnail) : null;
            $course->enrolled_students = $course->students()->count();

            foreach ($course->courseParts as $part) {
                foreach ($part->courseMaterials as $material) {
                    if ($material->type != "url") {
                        $material->url = asset('storage/' . $material->url);
                    }
                }
            }

            return $course;
        });

        return response()->json([
            'status' => 200,
            'student' => $student->name,
            'roll_no' => $student->roll_no,
            'courses' => $courses,
        ], 200);
    }


    public function delete($id)
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return redirect()->back()->with('error', 'Enrollment not found.');
        }

        $deleted = $enrollment->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Enrollment deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete enrollment.');
        }
    }


}
