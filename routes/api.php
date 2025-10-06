<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;

// Login route
Route::post('/auth/login', [AuthController::class, 'login']);

// Transactions
Route::get('/transactions', [TransactionsController::class, 'index'])->middleware('auth:sanctum');
Route::post('/transactions', [TransactionsController::class, 'store'])->middleware('auth:sanctum');
