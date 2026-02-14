<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;

Route::get('/', function () {
    return redirect()->route('students.index');
});

// Student routes
Route::prefix('students')->name('students.')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('index');
    Route::get('/create', [StudentController::class, 'create'])->name('create');
    Route::post('/', [StudentController::class, 'store'])->name('store');
    Route::get('/{student}', [StudentController::class, 'show'])->name('show');
    Route::post('/{student}/enroll', [StudentController::class, 'enroll'])->name('enroll');
    Route::delete('/{student}/courses/{course}', [StudentController::class, 'unenroll'])->name('unenroll');
});

// Course routes
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/create', [CourseController::class, 'create'])->name('create');
    Route::post('/', [CourseController::class, 'store'])->name('store');
    Route::get('/{course}', [CourseController::class, 'show'])->name('show');
    Route::post('/{course}/enroll-student', [CourseController::class, 'enrollStudent'])->name('enrollStudent');
    Route::delete('/{course}/students/{student}', [CourseController::class, 'removeStudent'])->name('removeStudent');
});