<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseDetailController;
use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;

// Google OAuth Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Route::get('/', function () {
//     return view('welcome');
// });

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




});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/user', [DashboardController::class, 'countUsers']);

require __DIR__.'/auth.php';
