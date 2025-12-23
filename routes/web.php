<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\libraryPortal\DashboardController;
use App\Http\Controllers\LibraryUserController;
use App\Http\Controllers\libraryPortal\UserMgtController;
use App\Http\Controllers\libraryPortal\FeesMgtController;
use App\Http\Controllers\libraryPortal\SeatMgtController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('/');
Route::controller(LoginController::class)->group(function () {
    Route::get('login', 'index')->name('login');
    Route::post('login/submit', 'loginSubmit')->name('login.submit');
    Route::get('login/otp', 'loginOtp')->name('login.otp');
    Route::post('login/emailverify/check', 'loginEmailverifyCheck')->name('login.emailverify.check');
    Route::match(['get', 'post'], '/login/otp/fill', 'loginOtpFill')->name('login.otp.fill');
    Route::post('login/otp/check', 'loginOtpCheck')->name('login.otp.check');
    Route::post('resend-otp', 'resendOtp')->name('login.resend.otp');
    Route::get('logout', 'logout')->name('logout');
});


Route::middleware(['auth'])->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'dashboard')->name('dashboard');
        Route::get('active-access-Code/download', 'activeAccessCodeDownload')->name('active-access-Code.download');
        Route::get('download-app', 'downloadApp')->name('sp.download-app.page');
        Route::get('user-manuals', 'userManual')->name('sp.user.manual');
    });

    Route::controller(UserMgtController::class)->group(function () {
        Route::get('student/manager', 'studentManager')->name('student.manager');
        Route::get('student/add', 'studentAdd')->name('student.add');
        Route::get('student/edit/{id}', 'studentEdit')->name('student.edit');
        Route::get('student/delete/{id}', 'studentDelete')->name('student.delete');
        Route::post('student/save', 'userSave')->name('sp.student.save');
        Route::get('/student/last-payment', 'lastPayment')
            ->name('student.last.payment');
    });
    Route::controller(SeatMgtController::class)->group(function () {
        Route::get('seat/list', 'index')->name('seat.index');
        Route::post('seat/assign', 'seatAssign')->name('seat.assign');
        Route::get('seat/edit/{id}', 'seatEdit')->name('seat.edit');
        Route::get('seat-assignment/delete/{id}', 'deleteSeatAssignment')->name('seat.assignment.delete');
        Route::post('seat/save', 'seatSave')->name('seat.save');
    });
    Route::controller(FeesMgtController::class)->group(function () {
        Route::get('fees/dashboard', 'dashboard')->name('fees.dashboard');
        Route::get('collect/fees', 'collectFees')->name('collect.fees');
        Route::get('collect-fees/edit/{id}', 'editCollectFees')->name('collect.fees.edit');
        Route::post('collect/fees', 'collectFeesSave')->name('collect.fees.save');
        Route::post('renew/fees', 'renewFeesSave')->name('renew.fees.save');
        Route::get('user/payment/history', 'userPaymentHistory')->name('user.payment.history');
    });

    Route::post('/notifications/{id}/read', function ($id) {
        auth()->user()->notifications()->where('id', $id)->update([
            'read_at' => now()
        ]);

        return response()->json(['success' => true]);
    })->name('notifications.read');
});
