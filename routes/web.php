<?php

use App\Http\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Transactions pages
    Route::get('/', [TransactionsController::class, 'transactionHistory'])->name('transaction_history.index');
    Route::get('/transfer', [TransactionsController::class, 'create'])->name('transfer.create');
    Route::post('/transfer', [TransactionsController::class, 'store'])->name('transfer.store');
});

require __DIR__.'/auth.php';
