<?php

use App\Http\Controllers\AppControllers\StudentAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AuthControllers\LoginController;
use App\Http\Controllers\AuthControllers\ResetPasswordController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizEnrollmentController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name("login.show");
    Route::post('/login', [LoginController::class, 'login'])->name("login");

    Route::get('/forgot-password', [ResetPasswordController::class, 'index'])->name("forgot");
    Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLinkEmail'])->name("reset.send");
    Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name("reset.show");
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name("reset.post");
    Route::post('/reset-successful', [ResetPasswordController::class, 'resetPassword'])->name("reset.successful");
});

Route::middleware('auth')->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name("dashboard");
    Route::get('/logout', [LoginController::class, 'logout'])->name("logout");

    Route::get('/students', [StudentAuthController::class, 'manage'])->name('students');
    Route::delete('/students/{id}', [StudentAuthController::class, 'deleteStudent'])->name('delete.student');

    Route::get('/create-category', [CategoryController::class, 'index'])->name('create.category');
    Route::post('/create-category', [CategoryController::class, 'create'])->name('post.category');
    Route::put('/update-category/{id}', [CategoryController::class, 'update'])->name('update.category');
    Route::get('/categories', [CategoryController::class, 'show'])->name('show.categories');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('delete.category');

    Route::get('/create-instructor', [InstructorController::class, 'index'])->name('create.instructor');
    Route::post('/create-instructor', [InstructorController::class, 'create'])->name('post.instructor');
    Route::get('/instructors', [InstructorController::class, 'show'])->name('show.instructor');
    Route::delete('/instructors/{id}', [InstructorController::class, 'destroy'])->name('destroy.instructor');
    Route::put('/instructors/update', [InstructorController::class, 'update'])->name('update.instructor');

    Route::get('/create-course', [CourseController::class, 'index'])->name('create.course');
    Route::get('/courses', [CourseController::class, 'showCourses'])->name('courses.show');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{courseid}/create-parts', [CourseController::class, 'createParts'])->name('courses.createParts');
    Route::post('/courses/store-parts', [CourseController::class, 'storeParts'])->name('courses.storeParts');
    Route::get('/courses/{courseid}/upload-content', [CourseController::class, 'uploadContent'])->name('courses.uploadContent');
    Route::post('/courses/store-content', [CourseController::class, 'storeContent'])->name('courses.storeContent');
    Route::put('/courses/update/{id}', [CourseController::class, 'update'])->name('courses.update');
    Route::get('/courses/update/{id}/parts', [CourseController::class, 'getCourseParts'])->name('update.courses.parts');
    Route::delete('/course-parts/{id}', [CourseController::class, 'deleteCoursePart'])->name('courseParts.delete');
    Route::put('/course-parts/update/{id}', [CourseController::class, 'updatePart'])->name('courseParts.update');
    Route::get('/courses/{courseId}/manage-lessons', [CourseController::class, 'manageLessons'])->name('courses.manageLessons');
    Route::put('/lessons/{lessonId}/update', [CourseController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{lessonId}', [CourseController::class, 'deleteLesson'])->name('lessons.delete');
    Route::delete('/courses/{id}', [CourseController::class, 'deleteCourse'])->name('courses.delete');

    Route::get('/create-quiz', [QuizController::class, 'create'])->name('create.quiz');
    Route::post('/create-quiz', [QuizController::class, 'import'])->name('store.quiz');
    Route::get('/manage-quiz', [QuizController::class, 'manageQuizzes'])->name('manage.quiz');
    Route::delete('/quizzes/{id}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::put('/quizzes/{id}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::patch('/quizzes/{id}/status', [QuizController::class, 'updateStatus'])->name('quizzes.updateStatus');

    Route::get('/add-enrollment', [EnrollmentController::class, 'create'])->name('add.enrollment');
    Route::post('/add-enrollment', [EnrollmentController::class, 'store'])->name('store.enrollment');
    Route::get('/manage-enrollment', [EnrollmentController::class, 'manage'])->name('manage.enrollment');
    Route::delete('/enrollments/{id}', [EnrollmentController::class, 'delete'])->name('enrollments.delete');

    Route::get('/add-quiz-enrollment', [QuizEnrollmentController::class, 'create'])->name('add.quiz.enrollment');
    Route::post('/add-quiz-enrollment', [QuizEnrollmentController::class, 'store'])->name('store.quiz.enrollment');
    Route::get('/manage-quiz-enrollment', [QuizEnrollmentController::class, 'manage'])->name('manage.quiz.enrollment');
    Route::delete('/quiz-enrollments/{id}', [QuizEnrollmentController::class, 'delete'])->name('quiz.enrollment.delete');

    Route::get('/settings', [SettingsController::class, 'settings'])->name('settings');
    Route::put('/settings/update-name/{id}', [SettingsController::class, 'updateUserName'])->name('updateUserName');
    Route::put('/settings/update-email/{id}', [SettingsController::class, 'updateUserEmail'])->name('updateUserEmail');
    Route::put('/settings/update-about/{id}', [SettingsController::class, 'updateInstructorAbout'])->name('updateInstructorAbout');
    Route::put('/settings/update-skills/{id}', [SettingsController::class, 'updateInstructorSkills'])->name('updateInstructorSkills');
    Route::put('/settings/update-password/{id}', [SettingsController::class, 'updatePassword'])->name('updatePassword');
});
