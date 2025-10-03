<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'data' => $transactions,
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

        // Create new transaction
        TransferService::transfer(Auth::id(), $data['receiver_id'], $data['amount']);

        return response()->json([
            'message' => 'Transactions stored successfully',
        ]);
    }
}
