<?php

use App\Http\Controllers\Api\user\LibraryApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\user\UserApiController;

Route::middleware('auth:sanctum')->get('user', function (Request $request) {
    return $request->user();
});

Route::controller(UserApiController::class)->group(function () {
    Route::post('user/create', 'userCreate');
});
Route::controller(LibraryApiController::class)->group(function () {
    Route::post('library/create', 'libraryCreate');
    Route::get('get/library', 'getLibrary');
});


Route::get('optimize', function () {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('route:clear');
    $exitCode = Artisan::call('view:clear');
    $exitCode = Artisan::call('optimize:clear');

    return '<h1>Web Cache Cleared</h1>';
});
