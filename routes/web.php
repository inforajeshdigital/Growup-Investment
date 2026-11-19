<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SettingsController;
use App\Models\Bill;
use Illuminate\Support\Facades\Route;

Route::get('/install/{key}', InstallController::class)->middleware('throttle:5,1');

// Public company profile (shareable link)
Route::get('/about', [CompanyProfileController::class, 'show'])->name('about');
Route::get('/about/company-profile.pdf', [CompanyProfileController::class, 'pdf'])->name('about.pdf');
Route::get('/company-logo', [CompanyProfileController::class, 'logo'])->name('company.logo');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.forgot');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('password.send-otp')->middleware('throttle:6,10');
    Route::get('/reset-password', [PasswordResetController::class, 'showReset'])->name('password.reset.form');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.reset')->middleware('throttle:10,10');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return view('dashboard', [
            'totalBills' => Bill::count(),
            'recentBills' => Bill::latest('id')->limit(5)->get(),
        ]);
    })->name('dashboard');

    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/settings/smtp',[SettingsController::class, 'updateMail'])->name('settings.mail.update');
    Route::post('/settings/smtp/test', [SettingsController::class, 'testMail'])->name('settings.mail.test')->middleware('throttle:5,1');
    Route::post('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.account.update');

    Route::get('/bills', [BillController::class, 'index'])->name('bills.index');
    Route::get('/bills/create', [BillController::class, 'create'])->name('bills.create');
    Route::post('/bills/preview', [BillController::class, 'preview'])->name('bills.preview');
    Route::get('/bills/preview', [BillController::class, 'showPreview'])->name('bills.preview.show');
    Route::post('/bills', [BillController::class, 'store'])->name('bills.store');
    Route::get('/bills/{bill}', [BillController::class, 'show'])->name('bills.show');
    Route::get('/bills/{bill}/edit', [BillController::class, 'edit'])->name('bills.edit');
    Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.update');
    Route::delete('/bills/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');
    Route::get('/bills/{bill}/pdf', [BillController::class, 'pdf'])->name('bills.pdf');
    Route::get('/bills/{bill}/print', [BillController::class, 'print'])->name('bills.print');
    Route::get('/bills/{bill}/income-proof', [BillController::class, 'incomeProof'])->name('bills.income-proof');
});
