<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});

Route::middleware(['auth', 'can:manage-gym'])->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::patch('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');

    Route::post('/membership-plans', [MembershipPlanController::class, 'store'])->name('plans.store');
    Route::patch('/membership-plans/{membershipPlan}', [MembershipPlanController::class, 'update'])->name('plans.update');
    Route::delete('/membership-plans/{membershipPlan}', [MembershipPlanController::class, 'destroy'])->name('plans.destroy');

    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::patch('/attendance/{attendance}/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
});
