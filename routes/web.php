<?php

use App\Http\Controllers\TransactionsCotrnoller;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth', 'verified'])->group(function () {
    // Transactions pages
    Route::get('/transaction_history', [TransactionsCotrnoller::class, 'transactionHistory'])->name('transaction_history.index');
    Route::get('/transfer', [TransactionsCotrnoller::class, 'create'])->name('transfer.create');
    Route::post('/transfer', [TransactionsCotrnoller::class, 'store'])->name('transfer.store');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
