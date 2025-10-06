<?php

namespace App\Http\Controllers;

use App\Events\TransferSaved;
use App\Http\Requests\TransferRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TransactionsController extends Controller
{
    /**
     * Default pagination size.
     *
     * @var int
     */
    private int $paginate = 10;

    /**
     * Return a paginated list of transactions for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $transactions = Transaction::with(['sender', 'receiver'])
            ->where(function ($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('receiver_id', Auth::id());
            })
            ->latest()
            ->paginate($this->paginate);

        return response()->json([
            'data' => $transactions,
        ]);
    }

    /**
     * Render the transaction history page for the current user.
     *
     * @return \Inertia\Response
     */
    public function transactionHistory()
    {
        $transactions = Transaction::with(['sender', 'receiver'])
            ->where(function ($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('receiver_id', Auth::id());
            })
            ->latest()
            ->paginate($this->paginate);

        return Inertia::render('transactions/History', [
            'transactions' => $transactions,
            'user' => Auth::user(),
            'pagination' => [
                'current_page'   => $transactions->currentPage(),
                'last_page'      => $transactions->lastPage(),
                'total'          => $transactions->total(),
                'next_page_url'  => $transactions->nextPageUrl(),
                'prev_page_url'  => $transactions->previousPageUrl(),
            ],
        ]);
    }

    /**
     * Show the "New Transfer" form.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        $user = Auth::user();

        return Inertia::render('transactions/Transfer', compact('users', 'user'));
    }

    /**
     * Handle new transfer request (both web and API).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(TransferRequest $request)
    {
        // Execute transfer via service layer
        $response = TransferService::transfer(
            Auth::id(),
            $request->receiver_id,
            $request->amount,
        );

        // Handle API requests (under /api prefix)
        if ($request->route()->getPrefix() === 'api') {
            $status = $response['success'] ? 201 : 422;
            return response()->json($response, $status);
        }

        // Handle web requests (Inertia)
        if (!$response['success']) {
            return redirect()
                ->route('transfer.create')
                ->with('message', $response['message']);
        }

        // Broadcast transfer event to other clients (via Pusher)
        broadcast(new TransferSaved($response))->toOthers();

        return redirect()
            ->route('transaction_history.index')
            ->with('message', $response['message']);
    }
}
