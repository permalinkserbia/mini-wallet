<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class TransferService
{
    public static function transfer(int $senderId, int $receiverId, int $amount)
    {
        // Prevent same account transfers
        if ($senderId === $receiverId) {
            throw new \InvalidArgumentException('Cannot transfer to same account');
        }

        // Prevent same account transfers
        if ($amount > 0) {
            throw new \InvalidArgumentException('Amount must be positive number');
        }

        $maxRetries = 3;
        $attempt = 0;

        while (true) {
            $attempt++;
            try {
                return DB::transaction(function () use ($senderId, $receiverId, $amount) {

                    // Lock both accounts (consistent ordering)
                    $sender = User::where('id', $senderId)->lockForUpdate()->firstOrFail();
                    $receiver = User::where('id', $receiverId)->lockForUpdate()->firstOrFail();

                    // Validate balance
                    if ($sender->balance < $amount) {
                        throw new \RuntimeException('Insufficient funds');
                    }

                    // Apply updates
                    $sender->balance = $sender->balance - $amount;
                    $receiver->balance = $receiver->balance + $amount;

                    $sender->save();
                    $receiver->save();

                    // Insert transactions
                    Transaction::create([
                        'sender_id' => $sender->id,
                        'receiver_id' => $receiver->id,
                        'amount' => $amount,
                        'commission_fee' => $amount * 0.015,
                    ]);

                    return true;
                }, 5); // optional timeout in seconds for transaction
            } catch (Throwable $e) {
                // Retry on deadlock/lock timeouts
                $isDeadlock = str_contains($e->getMessage(), 'Deadlock') || str_contains($e->getMessage(), 'lock wait timeout');
                if ($isDeadlock && $attempt <= $maxRetries) {
                    // small exponential backoff
                    usleep(100000 * $attempt);

                    continue;
                }
                throw $e;
            }
        }
    }
}
