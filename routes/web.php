<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseDetailController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/documentation', function () {
    return view('documentation');
})->name('documentation');

// Google OAuth Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::post('/expenses', [DashboardController::class, 'store'])->name('expenses.store');
    Route::get('/chart-data', [DashboardController::class, 'chartData'])->name('chart.data');
    Route::get('/chart-category-data', [DashboardController::class, 'chartDataCategory'])->name('chart.category.data');
    Route::put('/expenses/{id}', [DashboardController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{id}', [DashboardController::class, 'destroy'])->name('expenses.destroy');
    Route::post('/expenses/{id}/toggle-realized', [DashboardController::class, 'toggleRealized'])->name('expenses.toggle-realized');

    Route::get('/expense/{id}/details', [ExpenseDetailController::class, 'index']);
    Route::post('/expense/detail', [ExpenseDetailController::class, 'store']);
    Route::delete('/expense/detail/{id}', [ExpenseDetailController::class, 'destroy']);
    Route::post('/expense/detail/{id}/check', [ExpenseDetailController::class, 'check']);

    Route::post('/income', [DashboardController::class, 'storeIncome'])->name('income.store');
    Route::put('/income', [DashboardController::class, 'updateIncome'])->name('income.update');
    
    // Additional Income
    Route::post('/income/transaction', [DashboardController::class, 'storeAdditionalIncome'])->name('income.transaction.store');
    Route::delete('/income/transaction/{id}', [DashboardController::class, 'destroyAdditionalIncome'])->name('income.transaction.destroy');


    Route::get('/summary/alokasi', [DashboardController::class, 'getDataAlokasi'])->name('summary.alokasi');
    Route::get('/summary/realisasi', [DashboardController::class, 'getDataRealisasi'])->name('summary.realisasi');
    Route::get('/summary/income', [DashboardController::class, 'getIncome'])->name('summary.income');
    
    // Copy Previous Month
    Route::post('/copy-previous-month', [DashboardController::class, 'copyPreviousMonth'])->name('copy.previous.month');

    // Quotes Routes
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');

    // Categories Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Debts Routes
    Route::get('/debts', [App\Http\Controllers\DebtController::class, 'index'])->name('debts.index');
    Route::post('/debts', [App\Http\Controllers\DebtController::class, 'store'])->name('debts.store');
    Route::put('/debts/{id}', [App\Http\Controllers\DebtController::class, 'update'])->name('debts.update');
    Route::delete('/debts/{id}', [App\Http\Controllers\DebtController::class, 'destroy'])->name('debts.destroy');
    Route::post('/debts/{id}/toggle-paid', [App\Http\Controllers\DebtController::class, 'markAsPaid'])->name('debts.toggle-paid');

    // Savings Routes
    Route::get('/savings', [App\Http\Controllers\SavingController::class, 'index'])->name('savings.index');
    Route::post('/savings', [App\Http\Controllers\SavingController::class, 'store'])->name('savings.store');
    Route::put('/savings/{id}', [App\Http\Controllers\SavingController::class, 'update'])->name('savings.update');
    Route::delete('/savings/{id}', [App\Http\Controllers\SavingController::class, 'destroy'])->name('savings.destroy');
    Route::post('/savings/{id}/deposit', [App\Http\Controllers\SavingController::class, 'deposit'])->name('savings.deposit');



    Route::post('/ai/ask', [\App\Http\Controllers\Api\AiFinanceController::class, 'ask'])->name('ai.ask');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/user', [DashboardController::class, 'countUsers']);

require __DIR__.'/auth.php';
