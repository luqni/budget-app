<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/expenses', [DashboardController::class, 'store'])->name('expenses.store');
Route::get('/chart-data', [DashboardController::class, 'chartData'])->name('chart.data');
Route::put('/expenses/{id}', [DashboardController::class, 'update'])->name('expenses.update');
Route::delete('/expenses/{id}', [DashboardController::class, 'destroy'])->name('expenses.destroy');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
