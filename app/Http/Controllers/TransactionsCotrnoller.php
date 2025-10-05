<?php

namespace App\Http\Controllers;

use App\Events\TransferSaved;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionsCotrnoller extends Controller
{
    private $paginate = 10;

    /**
     * Get all transactions
     */
    public function index()
    {
        // Logic to retrieve transactions
        $transactions = Transaction::with('sender', 'receiver')
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                ->orWhere('receiver_id', Auth::id());
            })
            ->latest()
            ->paginate($this->paginate);

        return response()->json([
            'data' => $transactions,
        ]);
    }


    public function transactionHistory()
    {
        $transactions = Transaction::with('sender', 'receiver')
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                ->orWhere('receiver_id', Auth::id());
            })
            ->latest()
            ->paginate($this->paginate);

        return Inertia::render('transactions/History', [
            'transactions' => $transactions,
            'user' => Auth::user(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'total' => $transactions->total(),
                'next_page_url' => $transactions->nextPageUrl(),
                'prev_page_url' => $transactions->previousPageUrl(),
            ],
        ]);
    }

    /**
     * Create new transfer
     */
    public function create()
    {
        $users = User::whereNot('id', Auth::id())->get();
        $user = Auth::user();
        return Inertia::render('transactions/Transfer', compact('users', 'user'));
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

        // If everything is correct trigger TransferSaved event and redirect user to Transaction History screen
        broadcast(new TransferSaved($response))->toOthers();

        return redirect()->route('transaction_history.index')->with('message', $response['message']);
    }
}
