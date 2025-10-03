<?php

use App\Http\Controllers\TransactionsCotrnoller;
use Illuminate\Support\Facades\Route;

// Transactions
Route::get('/transactions', [TransactionsCotrnoller::class, 'getTransactions'])->middleware('auth:sanctum');
