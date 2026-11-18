<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\SettingsController;
use App\Models\Bill;
use Illuminate\Support\Facades\Route;

Route::get('/install/{key}', InstallController::class)->middleware('throttle:5,1');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
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
