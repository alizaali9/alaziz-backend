<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AuthControllers\LoginController;
use App\Http\Controllers\AuthControllers\ResetPasswordController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, 'index'])->name("dashboard")->middleware('auth');
Route::get('/login', [LoginController::class, 'index'])->name("login.show")->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name("login")->middleware('guest');
Route::get('/logout', [LoginController::class, 'logout'])->name("logout")->middleware('auth');
Route::get('/forgot-password', [ResetPasswordController::class, 'index'])->name("forgot")->middleware('guest');
Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLinkEmail'])->name("reset.send")->middleware('guest');
Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name("reset.show")->middleware('guest');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name("reset.post")->middleware('guest');
Route::post('/reset-successful', [ResetPasswordController::class, 'resetPassword'])->name("reset.successful")->middleware('guest');

Route::get('/create-category', [CategoryController::class, 'index'])->middleware('auth')->name('create.category');
Route::post('/create-category', [CategoryController::class, 'create'])->middleware('auth')->name('post.category');
Route::put('/update-category/{id}', [CategoryController::class, 'update'])->middleware('auth')->name('update.category');
Route::get('/categories', [CategoryController::class, 'show'])->middleware('auth')->name('show.categories');
Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('delete.category');

Route::get('/create-instructor', [InstructorController::class, 'index'])->middleware('auth')->name('create.instructor');
Route::post('/create-instructor', [InstructorController::class, 'create'])->middleware('auth')->name('post.instructor');
Route::get('/instructors', [InstructorController::class, 'show'])->middleware('auth')->name('show.instructor');
Route::delete('/instructors/{id}', [InstructorController::class, 'destroy'])->name('destroy.instructor');
Route::put('/instructors/update', [InstructorController::class, 'update'])->name('update.instructor');

Route::get('/create-course', [CourseController::class, 'index'])->middleware('auth')->name('create.course');
Route::get('/courses', [CourseController::class, 'showCourses'])->name('courses.show');
Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
Route::get('/courses/{courseid}/create-parts', [CourseController::class, 'createParts'])->name('courses.createParts');
Route::post('/courses/store-parts', [CourseController::class, 'storeParts'])->name('courses.storeParts');
Route::get('/courses/{courseid}/upload-content', [CourseController::class, 'uploadContent'])->name('courses.uploadContent');
Route::post('/courses/store-content', [CourseController::class, 'storeContent'])->name('courses.storeContent');
// Route::post('/create-course', [CourseController::class, 'index'])->middleware('auth')->name('create.course');

Route::get('/create-quiz', [QuizController::class, 'create'])->middleware('auth')->name('create.quiz');
Route::post('/create-quiz', [QuizController::class, 'import'])->middleware('auth')->name('store.quiz');

Route::get('/add-enrollment', [EnrollmentController::class, 'create'])->middleware('auth')->name('add.enrollment');
Route::post('/add-enrollment', [EnrollmentController::class, 'store'])->middleware('auth')->name('store.enrollment');


