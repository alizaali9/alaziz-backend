<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Instructor;
use App\Models\Quiz;
use App\Models\Student;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        $students = Student::all();
        $courses = Course::all();
        $quizzes = Quiz::all();
        $openQuizzes = $quizzes->filter(function ($quiz) {
            return $quiz->status == 1;
        });
        $instructors = Instructor::all();
        return view('content.index', compact('students', 'openQuizzes', 'courses', 'instructors'));
    }
}
