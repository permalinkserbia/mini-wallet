<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TransactionsCotrnoller extends Controller
{
    /**
     * Get all transactions
     */
    public function index(Request $request)
    {
        // Logic to retrieve transactions
        $transactions = Transaction::with('sender', 'receiver')->get();

        return response()->json([
            'data' => $transactions
        ]);
    }

    /**
     * Create new transaction
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        $data['sender_id'] = Auth::id();
        $data['commission_fee'] = $request->amount * 0.15;

        // Create new transaction
        Transaction::create($data);

        return response()->json([
            'message' => 'Transactions stored successfully'
        ]);
    }
}
