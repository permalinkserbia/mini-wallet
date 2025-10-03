<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionsCotrnoller;
use Illuminate\Support\Facades\Route;

// Login route
Route::post('/auth/login', [AuthController::class, 'login']);

// Transactions
Route::get('/transactions', [TransactionsCotrnoller::class, 'index'])->middleware('auth:sanctum');
Route::post('/transactions', [TransactionsCotrnoller::class, 'store'])->middleware('auth:sanctum');
