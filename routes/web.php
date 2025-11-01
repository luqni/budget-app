<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseDetailController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/expenses', [DashboardController::class, 'store'])->name('expenses.store');
Route::get('/chart-data', [DashboardController::class, 'chartData'])->name('chart.data');
Route::put('/expenses/{id}', [DashboardController::class, 'update'])->name('expenses.update');
Route::delete('/expenses/{id}', [DashboardController::class, 'destroy'])->name('expenses.destroy');

Route::get('/notes/{id}/details', [ExpenseDetailController::class, 'index']);
Route::post('/details', [ExpenseDetailController::class, 'store']);
Route::delete('/details/{id}', [ExpenseDetailController::class, 'destroy']);
Route::patch('/details/{id}/check', [ExpenseDetailController::class, 'check']);




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
