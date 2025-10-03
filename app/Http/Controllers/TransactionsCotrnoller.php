<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionsCotrnoller extends Controller
{
    /**
     * Get all transactions
     */
    public function index()
    {
        // Logic to retrieve transactions
        $transactions = Transaction::with('sender', 'receiver')
            ->where('sender_id', Auth::id())
            ->where('receiver_id', Auth::id())
            ->get();

        return response()->json([
            'data' => $transactions,
        ]);
    }


    public function transactionHistory()
    {
        $transactions = Transaction::with('sender', 'receiver')
            ->where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->get();

        return Inertia::render('transactions/History', compact('transactions'));
    }

    /**
     * Create new transfer
     */
    public function create()
    {
        $users = User::whereNot('id', Auth::id())->get();
        return Inertia::render('transactions/Transfer', compact('users'));
    }

    /**
     * Store new transfer
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        // Create new transfer
        $response = TransferService::transfer(Auth::id(), $data['receiver_id'], $data['amount']);

        // Return JSON response to API call
        if ($request->route()->getPrefix() === 'api') {
            return response()->json($response);
        }

        // Return error to FE as JSON
        if (!$response['success']) {
            return response()->json($response);
        }

        // If everything is correct
        return redirect()->route('transaction_history.index')->with('message', $response['message']);
    }
}
