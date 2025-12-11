<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\user\LibraryApiController;
use App\Http\Controllers\Api\user\StudentApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\user\UserApiController;

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('/logout', 'logout');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(AuthController::class)
        ->group(function () {
            Route::post('/logout', 'logout');
        });

    Route::controller(UserApiController::class)->group(function () {
        Route::post('user/save', 'userCreate');
    });
    Route::controller(LibraryApiController::class)->group(function () {
        Route::post('library/save', 'librarySave');
        Route::get('get/libraries', 'getLibraries');
    });
    Route::controller(StudentApiController::class)->group(function () {
        Route::post('student/save', 'studentSave');
        Route::get('get/students', 'getStudents');
    });
});


Route::get('optimize', function () {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('optimize:clear');

    return '<h1>Web Cache Cleared</h1>';
});
