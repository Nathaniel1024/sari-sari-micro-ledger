<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LedgerController;
use App\Livewire\Admin\DebtSearch;
use App\Livewire\User\MyDebtStatus;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('customer.debt');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [LedgerController::class, 'index'])->name('dashboard');
        Route::get('/customers', [LedgerController::class, 'showCustomers'])->name('customers.index');
        Route::post('/customers', [LedgerController::class, 'storeCustomer'])->name('customers.store');

        Route::get('/credits', [LedgerController::class, 'createCredit'])->name('credits.create');
        Route::post('/credits', [LedgerController::class, 'storeCredit'])->name('credits.store');

        Route::get('/payments', [LedgerController::class, 'createPayment'])->name('payments.create');
        Route::post('/payments', [LedgerController::class, 'storePayment'])->name('payments.store');

        Route::get('/debt', [LedgerController::class, 'showDebtForm'])->name('debt.form');
        Route::post('/debt/check', [LedgerController::class, 'checkDebt'])->name('debt.check');

        Route::get('/debtors', DebtSearch::class)->name('debtors');
    });

    Route::middleware('customer')->group(function () {
        Route::get('/my-debt', MyDebtStatus::class)->name('customer.debt');
        Route::post('/my-debt/pay', [LedgerController::class, 'storeCustomerPayment'])->name('customer.payments.store');
    });
});
