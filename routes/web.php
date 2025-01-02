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
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SocialMediaLinkController;
use App\Http\Controllers\SubAdminController;
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
    Route::delete('/students/{id}', [StudentAuthController::class, 'deleteStudent'])->name('delete.student.admin');
    Route::get('/students/download-csv', [StudentAuthController::class, 'downloadCSV'])->name('students.downloadCSV');
    Route::get('/students/delete-account', [StudentAuthController::class, 'deleteStudentPage'])->name('delete.student.page');
    Route::delete('/students/delete-account', [StudentAuthController::class, 'deleteStudentAccount'])->name('delete.student');

    Route::get('/create-category', [CategoryController::class, 'index'])->name('create.category');
    Route::post('/create-category', [CategoryController::class, 'create'])->name('post.category');
    Route::put('/update-category/{id}', [CategoryController::class, 'update'])->name('update.category');
    Route::get('/categories', [CategoryController::class, 'show'])->name('show.categories');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('delete.category');
    Route::get('/categories/csv', [CategoryController::class, 'downloadCsv'])->name('download.csv');

    Route::get('/create-instructor', [InstructorController::class, 'index'])->name('create.instructor');
    Route::post('/create-instructor', [InstructorController::class, 'create'])->name('post.instructor');
    Route::get('/instructors', [InstructorController::class, 'manage'])->name('show.instructor');
    Route::get('/download-instructors', [InstructorController::class, 'downloadCSV'])->name('download.instructor');
    Route::delete('/delete-instructors/{id}', [InstructorController::class, 'destroy'])->name('destroy.instructor');
    Route::put('/instructors/update', [InstructorController::class, 'update'])->name('update.instructor');

    Route::get('/create-subadmin', [SubAdminController::class, 'index'])->name('create.subadmin');
    Route::post('/create-subadmin', [SubAdminController::class, 'create'])->name('post.subadmin');
    Route::get('/subadmins', [SubAdminController::class, 'manage'])->name('show.subadmin');
    Route::get('/download-subadmins', [SubAdminController::class, 'downloadCSV'])->name('download.subadmin');
    Route::delete('/subadmins/{id}', [SubAdminController::class, 'destroy'])->name('destroy.subadmin');
    Route::put('/subadmins/update', [SubAdminController::class, 'update'])->name('update.subadmin');


    Route::get('/create-course', [CourseController::class, 'index'])->name('create.course');
    Route::get('/courses', [CourseController::class, 'showCourses'])->name('courses.show');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{courseid}/create-parts', [CourseController::class, 'createParts'])->name('courses.createParts');
    Route::post('/courses/store-parts', [CourseController::class, 'storeParts'])->name('courses.storeParts');
    Route::get('/courses/{courseid}/upload-content', [CourseController::class, 'uploadContent'])->name('courses.uploadContent');
    Route::post('/courses/store-content', [CourseController::class, 'storeContent'])->name('courses.storeContent');
    Route::post('/course-materials/{id}/move-up', [CourseController::class, 'moveUp'])->name('course-materials.moveUp');
    Route::post('/course-materials/{id}/move-down', [CourseController::class, 'moveDown'])->name('course-materials.moveDown');
    Route::put('/courses/update/{id}', [CourseController::class, 'update'])->name('courses.update');
    Route::get('/courses/update/{id}/parts', [CourseController::class, 'getCourseParts'])->name('update.courses.parts');
    Route::post('/course-parts/{id}/move-up', [CourseController::class, 'moveUpPart'])->name('course-parts.moveUp');
    Route::post('/course-parts/{id}/move-down', [CourseController::class, 'moveDownPart'])->name('course-parts.moveDown');
    Route::delete('/course-parts/{id}', [CourseController::class, 'deleteCoursePart'])->name('courseParts.delete');
    Route::put('/course-parts/update/{id}', [CourseController::class, 'updatePart'])->name('courseParts.update');
    Route::get('/courses/{courseId}/manage-lessons', [CourseController::class, 'manageLessons'])->name('courses.manageLessons');
    Route::put('/lessons/{lessonId}/update', [CourseController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{lessonId}', [CourseController::class, 'deleteLesson'])->name('lessons.delete');
    Route::delete('/courses/{id}', [CourseController::class, 'deleteCourse'])->name('courses.delete');
    Route::get('/courses/csv', [CourseController::class, 'downloadCsv'])->name('courses.download.csv');
    Route::get('courses/{courseid}/parts/csv', [CourseController::class, 'downloadPartsCsv'])->name('parts.download.csv');
    Route::get('courses/{courseid}/lessons/csv', [CourseController::class, 'downloadLessonsCSV'])->name('lessons.download.csv');

    Route::get('/create-quiz', [QuizController::class, 'create'])->name('create.quiz');
    Route::get('/download-quizzes', [QuizController::class, 'downloadQuizzesCSV'])->name('download.quizzes');
    Route::post('/create-quiz', [QuizController::class, 'import'])->name('store.quiz');
    Route::get('/manage-quiz', [QuizController::class, 'manageQuizzes'])->name('manage.quiz');
    Route::delete('/quizzes/{id}', [QuizController::class, 'destroy'])->name('quizzes.destroy');
    Route::put('/quizzes/update/{id}', [QuizController::class, 'update'])->name('quizzes.update');
    Route::patch('/quizzes/{id}/status', [QuizController::class, 'updateStatus'])->name('quizzes.updateStatus');

    Route::get('/add-enrollment', [EnrollmentController::class, 'create'])->name('add.enrollment');
    Route::post('/add-enrollment', [EnrollmentController::class, 'store'])->name('store.enrollment');
    Route::get('/manage-enrollment', [EnrollmentController::class, 'manage'])->name('manage.enrollment');
    Route::delete('/enrollments/{id}', [EnrollmentController::class, 'delete'])->name('enrollments.delete');
    Route::patch('/enrollments/{id}/toggle-status', [EnrollmentController::class, 'toggleStatus']);

    Route::get('/add-quiz-enrollment', [QuizEnrollmentController::class, 'create'])->name('add.quiz.enrollment');
    Route::post('/add-quiz-enrollment', [QuizEnrollmentController::class, 'store'])->name('store.quiz.enrollment');
    Route::get('/manage-quiz-enrollment', [QuizEnrollmentController::class, 'manage'])->name('manage.quiz.enrollment');
    Route::delete('/quiz-enrollments/{id}', [QuizEnrollmentController::class, 'delete'])->name('quiz.enrollment.delete');
    Route::patch('/quiz-enrollments/{id}/toggle-status', [QuizEnrollmentController::class, 'toggleStatus']);

    Route::get('/settings', [SettingsController::class, 'settings'])->name('settings');
    Route::put('/settings/update-name/{id}', [SettingsController::class, 'updateUserName'])->name('updateUserName');
    Route::put('/settings/update-email/{id}', [SettingsController::class, 'updateUserEmail'])->name('updateUserEmail');
    Route::put('/settings/update-about/{id}', [SettingsController::class, 'updateInstructorAbout'])->name('updateInstructorAbout');
    Route::put('/settings/update-skills/{id}', [SettingsController::class, 'updateInstructorSkills'])->name('updateInstructorSkills');
    Route::put('/settings/update-password/{id}', [SettingsController::class, 'updatePassword'])->name('updatePassword');
    Route::put('/settings/update-picture/{id}', [SettingsController::class, 'updateInstructorPic'])->name('updateInstructorPic');

    Route::get('sliders/create', [SliderController::class, 'index'])->name('sliders.create');
    Route::post('sliders/store', [SliderController::class, 'store'])->name('sliders.store');
    Route::get('sliders/manage', [SliderController::class, 'manage'])->name('sliders.manage');
    Route::delete('sliders/{id}', [SliderController::class, 'destroy'])->name('sliders.destroy');

    Route::get('social-links/create', [SocialMediaLinkController::class, 'create'])->name('social.links.create');
    Route::post('social-links/store', [SocialMediaLinkController::class, 'store'])->name('social.links.store');
    Route::get('social-links/manage', [SocialMediaLinkController::class, 'manage'])->name('social.links.manage');
    Route::put('social-links/{id}/update', [SocialMediaLinkController::class, 'update'])->name('social.links.update');
    Route::delete('social-links/{id}/delete', [SocialMediaLinkController::class, 'delete'])->name('social.links.delete');
});
