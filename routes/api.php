<?php

use App\Http\Controllers\AppControllers\StudentAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;


Route::post('register', [StudentAuthController::class, 'register']);
Route::post('login', [StudentAuthController::class, 'login']);
Route::post('forgot-password', [StudentAuthController::class, 'forgotPassword']);

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




