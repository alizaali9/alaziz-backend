<?php

use App\Http\Controllers\AppControllers\StudentAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizEnrollmentController;
use App\Http\Controllers\SliderController;
use Illuminate\Support\Facades\Route;

Route::post('register', [StudentAuthController::class, 'register']);
Route::post('login', [StudentAuthController::class, 'login']);
Route::post('forgot-password', [StudentAuthController::class, 'forgotPassword']);

Route::middleware(['student.check'])->group(function () {
    Route::post('/get-student', [StudentAuthController::class, 'getStudentByRollNumber']);
    Route::put('/student/{rollNumber}', [StudentAuthController::class, 'editStudent']);
    Route::post('/upload-picture', [StudentAuthController::class, 'uploadPicture']);
    Route::get('/student/{roll_no}/courses', [EnrollmentController::class, 'getCoursesOfStudent']);
    Route::get('/student/{roll_no}/quizzes', [QuizEnrollmentController::class, 'getQuizzesOfStudent']);
    Route::put('/students/{roll_no}/quizzes/{quizId}/marks', [QuizEnrollmentController::class, 'updateMarksPercentage']);
    Route::delete('/student/delete/{roll_no}', [StudentAuthController::class, 'deleteStudentAPI']);


    Route::get('courses', [CourseController::class, 'getAllCourses']);
    Route::get('courses/{id}', [CourseController::class, 'getCourseDetails']);
    Route::put('courses/{course}/ratings', [CourseController::class, 'updateRatings']);

    Route::get('instructors', [InstructorController::class, 'getAllInstructors']);
    Route::get('instructors/{id}', [InstructorController::class, 'getInstructor']);

    Route::get('categories', [CategoryController::class, 'getAllCategories']);
    Route::get('categories/{id}/courses', [CategoryController::class, 'getCategoryCourses']);

    Route::get('enrollments', [EnrollmentController::class, 'index']);
    Route::post('enrollments/check', [EnrollmentController::class, 'checkEnrollment']);

    Route::get('quizzes', [QuizController::class, 'getAllQuizes']);
    Route::get('quizzes/{id}', [QuizController::class, 'getQuizById']);

    Route::get('quiz-enrollments', [QuizEnrollmentController::class, 'index']);
    Route::post('check-quiz-enrollment', [QuizEnrollmentController::class, 'checkEnrollment']);

    Route::get('sliders', [SliderController::class, 'getSliders']);
});






