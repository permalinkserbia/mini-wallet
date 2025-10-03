<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionsCotrnoller extends Controller
{
    public function getTransactions(Request $request)
    {
        // Logic to retrieve transactions
        $transactions = Transaction::with('sender', 'receiver')->all();
        return response()->json([
            'data' => $transactions,
            'message' => 'Transactions retrieved successfully'
        ]);
    }
}
