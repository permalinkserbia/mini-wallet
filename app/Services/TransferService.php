<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransferService
{
    /**
     * Perform a money transfer between two users.
     *
     * This method ensures atomicity using a database transaction,
     * handles potential deadlocks by retrying, and validates
     * input conditions before making any updates.
     *
     * @param int $senderId
     * @param int $receiverId
     * @param float $amount
     * @return array
     */
    public static function transfer(int $senderId, int $receiverId, float $amount): array
    {
        // Prevent transfers to the same account
        if ($senderId === $receiverId) {
            return [
                'success' => false,
                'message' => 'Cannot transfer to the same account.',
            ];
        }

        // Prevent negative or zero transfers
        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'Amount must be a positive number.',
            ];
        }

        $maxRetries = 3;
        $attempt = 0;

        // Retry loop to handle deadlocks or lock timeouts
        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                return DB::transaction(function () use ($senderId, $receiverId, $amount) {
                    // Lock both accounts to prevent concurrent balance updates
                    $sender = User::where('id', $senderId)->lockForUpdate()->firstOrFail();
                    $receiver = User::where('id', $receiverId)->lockForUpdate()->firstOrFail();

                    // Validate sender's balance
                    if ($sender->balance < $amount) {
                        throw new \RuntimeException('Insufficient funds.');
                    }

                    // Calculate commission fee (1.5%)
                    $commissionFee = $amount * 0.015;

                    // Update balances
                    $sender->decrement('balance', $amount + $commissionFee);
                    $receiver->increment('balance', $amount);

                    // Record the transaction
                    Transaction::create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $receiver->id,
                        'amount' => $amount,
                        'commission_fee' => $commissionFee,
                    ]);

                    return [
                        'success' => true,
                        'receiver_id' => $receiver->id,
                        'amount' => $amount,
                        'message' => 'Transfer completed successfully.',
                    ];
                }, 5); // Optional timeout in seconds
            } catch (Throwable $e) {
                // Check if it's a recoverable database deadlock or lock timeout
                $isDeadlock = str_contains($e->getMessage(), 'Deadlock') ||
                              str_contains($e->getMessage(), 'lock wait timeout');

                if ($isDeadlock && $attempt < $maxRetries) {
                    // Exponential backoff before retrying
                    usleep(100000 * $attempt);
                    continue;
                }

                // Return failure message on final attempt or non-deadlock errors
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        }

        // Fallback in case retries are exhausted
        return [
            'success' => false,
            'message' => 'Transfer failed after multiple attempts.',
        ];
    }
}
