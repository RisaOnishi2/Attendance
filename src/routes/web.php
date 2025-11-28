<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\Admin\AttendanceApprovalController;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\StampCorrectionRequestController;

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ユーザー登録
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// 一般ユーザーログイン画面
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// 一般ユーザー用ログアウト
Route::post('/logout', LogoutController::class)->name('logout');

// 管理者ユーザーログイン画面
Route::prefix('admin')->group(function () {
    Route::get('/login', [App\Http\Controllers\LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/login', [App\Http\Controllers\LoginController::class, 'adminLogin']);
});

// 管理者用ログアウト
Route::prefix('admin')->group(function () {
    Route::post('/logout', App\Http\Controllers\AdminLogoutController::class)->name('admin.logout');
});

// 一般ユーザートップ画面用ルート表示
Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', function () {
        return view('attendance');
    })->name('attendance');
});

// 管理者ユーザートップ画面用ルート表示
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/admin/attendance/list', function () {
        return view('admin.attendance.list');
    })->name('admin.attendance.list');
});

Route::middleware(['auth'])->group(function () {
    // 一般ユーザー勤怠登録画面
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('index');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('clockIn');
    Route::post('/attendance/start-break', [AttendanceController::class, 'startBreak'])->name('startBreak');
    Route::post('/attendance/end-break', [AttendanceController::class, 'endBreak'])->name('endBreak');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('clockOut');

    // 一般ユーザー申請一覧
    Route::get('/stamp_correction_request/list', [AttendanceRequestController::class, 'index'])->name('requests');

    // 一般ユーザー勤怠一覧
    Route::get('/attendance/list/{year?}/{month?}', [AttendanceController::class, 'list'])
    ->name('list');

    // 一般ユーザー勤怠詳細
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])
    ->name('show');
    Route::put('/attendance/{id}', [AttendanceController::class, 'update'])->name('update');

    // 一般ユーザー勤怠申請一覧
    Route::middleware(['auth', 'user'])->group(function () {
        Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])
            ->name('stamp_correction_request.list');
    });

    // 管理者ユーザー日次勤怠一覧
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');
    });

    // 管理者ユーザースタッフ一覧
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/staff/list', [StaffController::class, 'index'])->name('admin.staff.list');
    });

    // 管理者ユーザースタッフ別勤怠一覧
    Route::middleware(['auth'])->prefix('admin')->group(function () {
        Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'show'])
        ->name('admin.attendance.staff.show');
    });

    // 管理者ユーザー勤怠申請一覧
    Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/stamp_correction_request/list', [AdminRequestController::class, 'index'])
            ->name('admin.requests.index');
    });

    // 管理者ユーザー修正申請承認画面
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::match(['get', 'post'], '/stamp_correction_request/{attendance_correct_request}', [AttendanceApprovalController::class, 'handle'])
            ->name('admin.stamp_correction_request.approve');
    });

});
